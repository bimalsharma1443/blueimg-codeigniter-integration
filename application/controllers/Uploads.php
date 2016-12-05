<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Uploads extends CI_Controller {

	public function __construct()
    	{
        	parent::__construct();
        	$this->load->helper(array('form', 'url'));
            $this->load->helper('directory');
            $this->load->helper("file");
    	}

    public function index(){
        $this->load->view('upload');
    }

    public function create_check_thubnail(){
        if(!is_dir(UPLOAD_THUMBNAIL_PATH)){
            mkdir(UPLOAD_THUMBNAIL_PATH,0777,true);
        }
    }

    public function upload_file(){
        $config['upload_path']          =UPLOAD_PATH;
        $config['allowed_types']        = 'gif|jpg|png';
        $config['max_size']             = 10000;
        return $config;
    }

    public function upload_thumbnail_file(){
        $config['upload_path']          =UPLOAD_THUMBNAIL_PATH;
        $config['allowed_types']        = 'gif|jpg|png';
        $config['max_size']             = 10000;
        return $config;
    }

    public function resize_tumbnail_file($image){
        $config['image_library'] = 'gd2';
        $config['source_image'] = $image;
        $config['maintain_ratio'] = TRUE;
        $config['width']         = 75;
        $config['height']       = 50;

        $this->load->library('image_lib', $config);

        $this->image_lib->resize();
    }

	public function upload(){
       
        $files = array();
        $filesCount = count($_FILES['files']['name']);
        $this->create_check_thubnail();

        for($i=0;$i<$filesCount;$i++){
            $file = new \stdClass();
            $_FILES['files']['name'] = $_FILES['files']['name'][$i];
            $_FILES['files']['type'] = $_FILES['files']['type'][$i];
            $_FILES['files']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
            $_FILES['files']['error'] = $_FILES['files']['error'][$i];
            $_FILES['files']['size'] = $_FILES['files']['size'][$i];

            $file->name = $_FILES['files']['name'];
            $file->size = $_FILES['files']['size'];
            $file->type = $_FILES['files']['type'];

            $config=$this->upload_file();

            $this->load->library('upload', $config);

             if ( ! $this->upload->do_upload('files')){
                $file->error = array('error' => $this->upload->display_errors());
            }
            else{
                $data = array('upload_data' => $this->upload->data());
                $file->url =  $data['upload_data']['full_path'];
                $file->name = $data['upload_data']['file_name'];
                $file->deleteType = 'DELETE';
                $file->deleteUrl = UPLOAD_DELETE_URL.$data['upload_data']['file_name'];               
            }

            $configUpload=$this->upload_thumbnail_file();

            $this->load->library('upload', $configUpload);
            $this->upload->initialize($configUpload);

             if ( ! $this->upload->do_upload('files')){
                $file->error = array('error' => $this->upload->display_errors());
            }
            else{
                $data = array('upload_data' => $this->upload->data());
                $this->resize_tumbnail_file($data['upload_data']['full_path']);
                $file->thumbnailUrl =  UPLOAD_THUMBNAIL_URL.$data['upload_data']['file_name'];
                
            }
            $files['files'][$i] = $file;
            
        }
        echo json_encode($files);
        exit;
	}

    public function delete($filename){
        //var_dump('/var/www/html/codeigniter/uploads/'.$filename); die;
        $files = array();
        $models_info = get_file_info(UPLOAD_PATH.$filename);
        $file = new \stdClass();
        $file->deleteType = "Delete";
        $file->deleteUrl = UPLOAD_DELETE_URL.$filename;
        $file->name =  $models_info['name'];
        $file->thumbnailUrl = UPLOAD_THUMBNAIL_URL.$filename;
        $file->size=$models_info['size'];
        $files['files'][0] = $file;
        unlink(UPLOAD_THUMBNAIL_PATH.$filename);
        unlink(UPLOAD_PATH.$filename);
        echo json_encode($files); 
        exit;
    }

    public function get(){
        //$thubnail = directory_map('/var/www/html/codeigniter/uploads/thumbnail',1);
        $files = array();
        $image_dir = directory_map(UPLOAD_PATH);
        foreach ($image_dir as $key => $value) {
            $i=0;
            $file = new \stdClass();
            if(!is_array($value)){
                $file->deleteType = "Delete";
                $file->deleteUrl = UPLOAD_DELETE_URL.$value;
                $file->name =  $value;
                $file->thumbnailUrl = UPLOAD_THUMBNAIL_URL.$value;
                $file->url = UPLOAD_URL.$value;
                $files['files'][$i] = $file;
                $i++;
            }
        }
        echo json_encode($files);
        exit;
    }




}
