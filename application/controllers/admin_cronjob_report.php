<?php
/**
 * Created by PhpStorm.
 * User: hoangnd
 * Date: 17/05/2018
 * Time: 10:42
 */

set_time_limit(0);

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Admin_cronjob_report extends CI_Controller
{
    private $user_id;
    private $voxy_lesson_id;
    private $voxy_users_lessons_id;
    private $voxy_activities_id;
    private $offset;

    public function __construct()
    {
        parent::__construct();
        if(!$this->input->is_cli_request())
        {
            echo 'Not allowed'.PHP_EOL;
             exit();
        }
        $this->config->load('api_config');
        $this->load->model('m_admin_cronjob_report');
    }

    public function run_by_date_range($page = 1) {
        $yesterday = date('Y-m-d', strtotime( '- 1days'));
        $lessons = $this->get_users_lessons_by_date_range($yesterday, $page);
        if(count($lessons)) {
            foreach ($lessons as $item) {
                if($this->m_admin_cronjob_report->count_user_by_user_id($item['external_user_id']) == 0) {
                    $this->_myLog('voxy_user with ID:'. $item['external_user_id'].' not found, continue!');
                    continue;
                }
                $this->user_id = $item['external_user_id'];
//                $this->user_id = 1000042710; // hard coded
                $this->voxy_lesson_id = $this->voxy_lessons_handle($item['title']);
                $this->voxy_users_lessons_id = $this->voxy_users_lessons_handle($item);
                $this->_myLog('Checking lesson avtivities');
                if(($count_lesson_activities = count($item['activities'])) > 0) {
                    $this->_myLog($count_lesson_activities . ' activities found, processing');
                    foreach ($item['activities'] as $index => $activity) {
                        $this->voxy_activities_id = $this->voxy_activities_handle($activity['title'], $activity['mode']);
                        $this->voxy_users_activities_handle($activity['date_completed']);

                    }
                }
            }
            $page = $page + 1;
            $this->run_by_date_range($page);
        }
        exit('Lessons is empty, EXIT!'.PHP_EOL);
    }

    public function run_by_user_id($offset =0, $limit = 50) {
        $this->offset = $offset;
        $list_users_id = $this->m_admin_cronjob_report->getListIDVipUsers($offset, $limit);
        if(count($list_users_id)) {
            foreach ($list_users_id as $item) {
                $this->user_id = $item['user_id'];
//                $this->user_id = 1000042710; // hard coded
                $user_lessons = $this->get_user_lessons_by_user_id($this->user_id, '2010-01-01');
                foreach ($user_lessons as $lesson) {
                    $this->voxy_lesson_id = $this->voxy_lessons_handle($lesson['title']);
                    $this->voxy_users_lessons_id = $this->voxy_users_lessons_handle($lesson);
                    $this->_myLog('Checking lesson avtivities');
                    if(($count_lesson_activities = count($lesson['activities'])) > 0) {
                        $this->_myLog($count_lesson_activities . ' activities found, processing');
                        foreach ($lesson['activities'] as $index => $activity) {
                            $this->voxy_activities_id = $this->voxy_activities_handle($activity['title'], $activity['mode']);
                           $this->voxy_users_activities_handle($activity['date_completed']);

                        }
                    }

                }
            }
            $offset = $offset + $limit;
            $this->run_by_user_id($offset, $limit);
        }
        exit('List Vip users empty, EXIT!'.PHP_EOL);
    }

    private function get_users_lessons_by_date_range($from_date, $page) {
        $to_date = date('Y-m-d');
        $args = [
            'start_date' => $from_date,
            'end_date' => $to_date,
            'page' => $page
        ];
        $api_response =  $this->call_voxy_api_service('/lessons', $args);
        if($api_response['status'] == true) {
            return $api_response['data']['results'];
        } else {
            $this->_myLog($api_response['message'], true);
            return false;
        }

    }

    private function voxy_lessons_handle($lesson_title) {
        $this->_myLog('===================');
        $this->_myLog('voxy_lessons_handle');
        $this->_myLog('===================');
        $this->_myLog('Try to find exits records');
        $row_found = $this->m_admin_cronjob_report->findLessonByTitle($lesson_title);
        if(count($row_found)) {
            $row_id = $row_found['id'];
            $this->_myLog('Found a record, ID: '. $row_id);

        } else {
            $this->_myLog('Not found, try to insert new record' );
            $row_data = [
                'title' => $lesson_title,
                'created_at' => time(),
                'created_by' => -1
            ];
            if(($row_id = $this->m_admin_cronjob_report->insertDataToTable('voxy_lessons', $row_data)) == false) {
                $this->_myLog('Insert error, EXIT!', true);
            } else {
                $this->_myLog('Insert Success, new record ID: '. $row_id);
            }
            $this->_myLog('==== END [voxy_lessons_handle] ====');
        }
        return $row_id;
    }

    private function voxy_activities_handle($activity_title, $activity_mode) {
        $this->_myLog('===================');
        $this->_myLog('voxy_activities_handle');
        $this->_myLog('===================');
        $this->_myLog('Try to find exits records');
        $row_found = $this->m_admin_cronjob_report->findActivitiesByTitleAndMode($activity_title, $activity_mode);
        if(count($row_found)) {
            $row_id = $row_found['id'];
            $this->_myLog('Found a record, ID: '. $row_id);

        } else {
            $this->_myLog('Not found, try to insert new record' );
            $row_data = [
                'mode' => $activity_mode,
                'title' => $activity_title,
                'created_at' => time(),
                'created_by' => -1,
            ];
            if(($row_id = $this->m_admin_cronjob_report->insertDataToTable('voxy_activities', $row_data)) == false) {
                $this->_myLog('Insert error, EXIT!', true);
            } else {
                $this->_myLog('Insert Success, new record ID: '. $row_id);
            }
            $this->_myLog('==== END [voxy_activities_handle] ====');
        }
        return $row_id;
    }

    private function voxy_users_lessons_handle($lesson) {
        $this->_myLog('===================');
        $this->_myLog('voxy_users_lessons_handle');
        $this->_myLog('===================');
        $this->_myLog('Try to find exits records');
        $row_data = [
            'lesson_id' => $this->voxy_lesson_id,
            'user_id' => $this->user_id,
            'date_started' => strtotime($lesson['date_started']),
            'date_completed' => ($lesson['date_completed'] == null) ? null: strtotime($lesson['date_completed']),
            'reading_score' => $lesson['reading_score'],
            'grammar_score' => $lesson['grammar_score'],
            'vocabulary_score' => $lesson['vocabulary_score'],
            'listening_score' => $lesson['listening_score'],
            'writing_score' => $lesson['writing_score'],
            'pronunciation_score' => $lesson['pronunciation_score'],
            'spelling_score' => $lesson['spelling_score'],
            'fluency_score' => $lesson['fluency_score'],
            'speaking_score' => $lesson['speaking_score'],
            'total_score' => $lesson['total_score'],
            'created_by' => -1,
            'created_at' => time(),
        ];
        $row_found = $this->m_admin_cronjob_report->findUsersLessons($this->user_id, $this->voxy_lesson_id);
        if(count($row_found)) {
            $row_id = $row_found['id'];
            $this->db->where('id', $row_id);
            $this->db->update('voxy_users_lessons', $row_data);
            $this->_myLog('row updated, ID: '. $row_id);

        } else {
            $this->_myLog('Not found, try to insert new record' );
            if(($row_id = $this->m_admin_cronjob_report->insertDataToTable('voxy_users_lessons', $row_data)) == false) {
                $this->_myLog('Insert to voxy_users_lessons table error, EXIT!', true);
            } else {
                $this->_myLog('Insert Success, new record ID: '. $row_id);
            }
            $this->_myLog('==== END [voxy_users_lessons_handle] ====');

        }
        return $row_id;
    }

    private function voxy_users_activities_handle($date_completed) {
        $this->_myLog('===================');
        $this->_myLog('voxy_users_activities_handle');
        $this->_myLog('===================');
        $this->_myLog('Try to find exits records');
        $row_data = [
            'user_id' => $this->user_id,
            'activity_id' => $this->voxy_activities_id,
            'users_lesson_id' => $this->voxy_users_lessons_id,
            'date_completed' => strtotime($date_completed),
        ];
        $row_found = $this->m_admin_cronjob_report->findUserActivitiesByUserIdAndActivityId($this->user_id, $this->voxy_activities_id);
        if(count($row_found)) {
            $row_id = $row_found['id'];
            $this->db->where('id', $row_id);
            $this->db->update('voxy_users_activities', $row_data);
            $this->_myLog('row updated, ID: '. $row_id);

        } else {
            $row_data['created_at'] = time();
            $row_data['created_by'] = -1;
            $this->_myLog('Not found, try to insert new record' );
            if(($row_id = $this->m_admin_cronjob_report->insertDataToTable('voxy_users_activities', $row_data)) == false) {
                $this->_myLog('Insert to voxy_users_activities table error, EXIT!', true);
            } else {
                $this->_myLog('Insert Success, new record ID: '. $row_id);
            }
            $this->_myLog('==== END [voxy_users_activities_handle] ====');

            return $row_id;
        }
    }

    private function get_user_lessons_by_user_id($user_id, $date_from) {
        $args = [
            'start_date' => $date_from,
            'end_date' => date('Y-m-d'),
            'external_user_id' => $user_id,
            'page' => 1
        ];
        $rs = [];
        $api_response =  $this->call_voxy_api_service('/lessons', $args);
        if($api_response['status'] == true) {
            $rs = $api_response['data']['results'];
            if(count(@$rs)) {
                do {
                    $args['page'] = $args['page'] + 1;
                    $api_next_page_response = $this->call_voxy_api_service('/lessons', $args);
                    array_merge($rs, $api_next_page_response['data']['results']);
                } while($api_next_page_response['status'] == true && count(@$api_next_page_response['data']['results']));
            }
        } else {
            $this->_myLog($api_response['message'], true);
        }
        return array_reverse($rs);
    }

    private function call_voxy_api_service($api_url, $params = array()) {
        $rs = [
            'status' => true,
            'data' => [],
            'message' => 'success'
        ];

        // Get api config loaded
        $api_config = $this->config->item('api_voxy');
        if(!$api_config) {
            $rs['status'] = false;
            $rs['message'] = 'Không tìm thấy file api_config';
            return $rs;
        }

        $url_string = $this->get_url_string($params);

        if( strpos($api_url, $api_config['server']) === false ) {
            $url = $api_config['server'] . $api_url . '/?' . $url_string;
        }
        $this->_myLog('CURL to url: '. $url);
        $headers = [
            'AUTHORIZATION:Voxy '.$api_config['key'].':'.hash('sha256', $api_config['api_secret'].$url_string)
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
//        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $curl_response = curl_exec($ch);

        // Check curl error
        if(curl_error($ch))
        {
            $rs['status'] = false;
            $rs['data'] = curl_error($ch);
            $rs['message'] = 'CURL ERROR!';
            return $rs;
        }

        // Check response header
        if( ($http_code_response = curl_getinfo($ch, CURLINFO_HTTP_CODE)) !== 200) {
        // !200 -> error!
            $rs['status'] = false;
            $rs['data'] = $curl_response;
            $rs['message'] = 'HTTP HEADER: '. $http_code_response;
        } else {
        // Everything is fine, decode response data
            $rs['data'] = json_decode($curl_response, true);
        }
        $this->_myLog('CURL DONE with HTTP code: '. $http_code_response);
        return $rs;
    }

    /**
     * Ham convert array param sang url param de gui sang voxy
     *
     * @param array $body_params
     *
     * @return bool|string
     */
    private function get_url_string($body_params = array())
    {
        $string_url = '';
        if (!is_array($body_params)) {
            return $string_url;
        }
        ksort($body_params);
        foreach ($body_params as $key => $value) {
            if ($key == "email_address") {
                $string_url .= $key . '=' . str_replace('@', '%40', $value) . '&';
            } else {
                $string_url .= $key . '=' . $value . '&';
            }
        }
        $string_url = substr($string_url, 0, -1);
        return $string_url;
    }

    private function _myLog($text, $exit = false) {
        echo date('Y-m-d H:i:s'). ': '.$text.PHP_EOL;
        echo 'OFFSET: '. $this->offset . PHP_EOL;
        if($exit)
            exit();
    }
}