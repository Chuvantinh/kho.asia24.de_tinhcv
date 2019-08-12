<?php
/**
 * Created by PhpStorm.
 * User: hoangnd
 * Date: 17/05/2018
 * Time: 10:53
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class M_admin_cronjob_report extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getListIDVipUsers($offset = 0, $limit = 20) {
        $sql = "SELECT `user_id` FROM `voxy_users` LIMIT ?, ?";
        $query = $this->db->query($sql, array($offset, $limit));
        return $query->result_array();
    }

    public function insertDataToTable($table_name, $data) {
        if (empty($table_name) || empty($data)) return false;

        $this->db->insert($table_name, $data);
        if($this->db->affected_rows() > 0)
        {
            return $this->db->insert_id();
        }
        return false;

    }

    public function insertOnDuplicateUpdate($table_name, $data) {
        if (empty($table_name) || empty($data)) return false;
        $duplicate_data = array();
        foreach($data AS $key => $value) {
            $duplicate_data[] = sprintf("%s=%s", $key, $this->db->escape($value));
        }

        $sql = sprintf("%s ON DUPLICATE KEY UPDATE %s", $this->db->insert_string($table_name, $data), implode(',', $duplicate_data));
        $this->db->query($sql);
        if($this->db->affected_rows() > 0)
        {
            return $this->db->insert_id();
        } else {

        }
        //var_dump($this->db->insert_id());die;
        return false;
    }

    public function findLessonByTitle($lesson_title) {
        $sql = "SELECT * FROM `voxy_lessons` WHERE `title` = ?";
        $query = $this->db->query($sql, $lesson_title);
        return $query->row_array();
    }

    public function findUsersLessons($user_id, $lesson_id) {
        $sql = "SELECT * FROM `voxy_users_lessons` WHERE `lesson_id` = ? AND `user_id` = ?";
        $query = $this->db->query($sql, [$lesson_id, $user_id]);
        return $query->row_array();
    }

    public function findActivitiesByTitleAndMode($title, $mode) {
        $sql = "SELECT * FROM `voxy_activities` WHERE `title` = ? AND `mode` = ?";
        $query = $this->db->query($sql, [$title, $mode]);
        return $query->row_array();
    }

    public function findUsersActivitiesByUserIdAndActivityId($user_id, $activity_id) {
        $sql = "SELECT * FROM `voxy_users_activities` WHERE `user_id` = ? AND `activity_id` = ?";
        $query = $this->db->query($sql, [$user_id, $activity_id]);
        return $query->row_array();
    }

    public function findUserActivitiesByUserIdAndActivityId($user_id, $activity_id) {
        $sql = "SELECT * FROM `voxy_users_activities` WHERE `user_id` = ? AND `activity_id` = ?";
        $query = $this->db->query($sql, [$user_id, $activity_id]);
        return $query->row_array();
    }

    public function count_user_by_user_id($user_id) {
        $sql = "SELECT COUNT(*) AS cnt FROM `voxy_users` WHERE `user_id` = ?";
        $query = $this->db->query($sql, $user_id);
        return $query->row()->cnt;
    }
}