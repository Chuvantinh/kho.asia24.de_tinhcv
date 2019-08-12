<?php
//include_once('libs/fpdf.php');
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once APPPATH . "third_party/fpdf181/fpdf.php";
class Generate_pdf extends FPDF
{

// Page header
    function Header()
    {
        // Logo
        $this->Image(base_url("images/logo2.png"),10,-1,70);
        $this->AddFont('VNI-Souvir', '','VNI-Souvir.php');
        $this->SetFont('VNI-Souvir');
        // Move to the right
        $this->Cell(80);
        // Title
        $this->Cell(80,10,'Phiếu giao hàng',1,0,'C');
        // Line break
        $this->Ln(20);
    }

// Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        //$this->AddFont('VnTime', '','vntime.php');
        //$this->SetFont('Arial');

        $this->AddFont('VNI-Souvir', '','VNI-Souvir.php');
        $this->SetFont('VNI-Souvir');

        // Page number
        $this->Cell(0,10,'Trang '.$this->PageNo().'/{nb}',0,0,'C');
    }

    public function index(){
        $display_heading = array(
            'stt'=>'STT',
            'name'=>'Tên SP',
            'price'=> 'Gia Euro ',
            'sl'=> 'SL',
            'total_price'=> 'Tong tien',
            'location'=> 'Vi tri'
        );

        $this->load->model("m_voxy_package_orders");
        $pdf = new Generate_pdf();
        $date_time = $this->input->post('date_time');

        $result = $this->m_voxy_package_orders->get_data_pdf($date_time);
        //xu ly du lieu
        $_export = array();
        $i = 0;
        if($result == null){
            $pdf->Error('KHong co san pham vao ngay nay,xin moi quay lai chon ngay');
        }else{
            foreach ($result as $item){

                foreach (json_decode($item['line_items']) as $key2 => $item2 ){
                    $i++;
                    $_export[$i] = get_object_vars($item2);
                }
            }
        }

        //ghep location like key to sort
        $export = array();
        foreach($_export as $key => $item){

            if($item["location"] == false){
                $item["location"] = $key."_NULL";
            }
            $export[$item["location"]] = $item;
        }
        //ksort tag theo khoa, krsort giam theo khoa hehe :D
        ksort($export);

//header
        $pdf->AddPage();
//foter page
        $pdf->AliasNbPages();
        //$this->AddFont('Arial', '','vntime.php');
        //$this->SetFont('Arial');
        $this->AddFont('VNI-Souvir', '','VNI-Souvir.php');
        $this->SetFont('VNI-Souvir');
        $pdf->SetLeftMargin(20);
        $pdf->SetRightMargin(20);
        $i = 0;
        $pdf->Cell(20,10,'Ngay In Phieu Giao Hang: '.$date_time); $pdf->Ln();

        $pdf->Cell(20,5,'nguoi nhan');
        $pdf->Cell(80);
        $pdf->Cell(20,5,'Nguoi gui');
        $pdf->Ln();

        $pdf->Cell(20,5,'Liefe Adresse');
        $pdf->Cell(80);
        $pdf->Cell(20,5,'Liefe Adresse');
        $pdf->Ln();

        $pdf->Cell(20,5,'Liefe Adresse');
        $pdf->Cell(80);
        $pdf->Cell(20,5,'Liefe Adresse');
        $pdf->Ln();

        $pdf->Cell(20,5,'Liefe Adresse');
        $pdf->Cell(80);
        $pdf->Cell(20,5,'Liefe Adresse');
        $pdf->Ln();

        $pdf->Cell(20,5,'Liefe Adresse');
        $pdf->Cell(80);
        $pdf->Cell(20,5,'Liefe Adresse');
        $pdf->Ln();

        $pdf->Ln();
        foreach($display_heading as $key => $heading) {
            $i++;
            if($key == 'stt' || $key == 'sl'){
                $pdf->Cell(10,12,$heading,1);
            }
            elseif($key == 'name'){
                $pdf->Cell(100,12,$heading,1, 0,'C');
            }else{
                $pdf->Cell(20,12,$heading,1);
            }
        }
        // xuat du lieu
        $j = 0;
        foreach($export as $row) {
            $cellWidth=100;//wrapped cell width
            $cellHeight=10;//normal one-line cell height
            //check whether the text is overflowing
            if($pdf->GetStringWidth($row['title']) < $cellWidth){
                //if not, then do nothing
                $line=1;
            }else{
                //if it is, then calculate the height needed for wrapped cell
                //by splitting the text to fit the cell width
                //then count how many lines are needed for the text to fit the cell

                $textLength=strlen($row['title']);	//total text length
                $errMargin=10;		//cell width error margin, just in case
                $startChar=0;		//character start position for each line
                $maxChar=0;			//maximum character in a line, to be incremented later
                $textArray=array();	//to hold the strings for each line
                $tmpString="";		//to hold the string for a line (temporary)

                while($startChar < $textLength){ //loop until end of text
                    //loop until maximum character reached
                    while(
                        $pdf->GetStringWidth( $tmpString ) < ($cellWidth-$errMargin) &&
                        ($startChar+$maxChar) < $textLength ) {
                        $maxChar++;
                        $tmpString=substr($row['title'],$startChar,$maxChar);
                    }
                    //move startChar to next line
                    $startChar=$startChar+$maxChar;
                    //then add it into the array so we know how many line are needed
                    array_push($textArray,$tmpString);
                    //reset maxChar and tmpString
                    $maxChar=0;
                    $tmpString='';

                }
                //get number of line
                $line=count($textArray);
            }

            //write the cells
            $j++;
            $pdf->Ln();
            //$pdf->Cell(10,20,$j,1);
            $pdf->Cell(10,($line * $cellHeight),$j,1); //adapt height to number of lines
            //$pdf->Cell(60,($line * $cellHeight),$item[1],1,0); //adapt height to number of lines

            //use MultiCell instead of Cell
            //but first, because MultiCell is always treated as line ending, we need to
            //manually set the xy position for the next cell to be next to it.
            //remember the x and y position before writing the multicell
            $xPos=$pdf->GetX();
            $yPos=$pdf->GetY();
            $pdf->MultiCell($cellWidth,$cellHeight,$row['title'],1);

            //return the position for next cell next to the multicell
            //and offset the x with multicell width
            $pdf->SetXY($xPos + $cellWidth , $yPos);
            $pdf->Cell(20,($line * $cellHeight),$row['price'],1);
            $pdf->Cell(10,($line * $cellHeight),$row['quantity'],1);
            $pdf->Cell(20,($line * $cellHeight),$row['price'] * $row['quantity'],1);
            $pdf->Cell(20,($line * $cellHeight),$row['location'],1);
        }
        $pdf->Output();
    }
}


?>