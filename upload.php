<?php
header("Content-Type: text/html;charset=utf-8");
/******************************************************************************
参数说明:
$max_file_size  : 上传文件大小限制, 单位BYTE
$destination_folder : 上传文件路径
******************************************************************************/

//上传文件类型列表
$uptypes=array(
    'image/jpg',
    'image/jpeg',
    'image/png',
    'image/pjpeg',
    'image/gif',
    'image/bmp',
    'image/x-png'
);

$max_file_size=2000000;     //上传文件大小限制, 单位BYTE
$destination_folder="up/"; //上传文件路径

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['type']=='upload')
{
    if (!is_uploaded_file($_FILES["upfile"]['tmp_name']))
    //是否存在文件
    {
         echo "图片不存在!";
         exit;
    }

    $file = $_FILES["upfile"];
    if($max_file_size < $file["size"])
    //检查文件大小
    {
		echo '<script>parent.return_text(文件太大!)</script>';
        exit;
    }

    if(!in_array($file["type"], $uptypes))
    //检查文件类型
    {
		echo "<script>parent.return_text('文件类型不符! $file[type]')</script>";
        exit;
    }

    if(!file_exists($destination_folder))
    {
        mkdir($destination_folder);
    }

    $filename=$file["tmp_name"];
    $image_size = getimagesize($filename);
    $pinfo=pathinfo($file["name"]);
    $ftype=$pinfo['extension'];
    $destination = $destination_folder.time().".".$ftype;
    if (file_exists($destination) && $overwrite != true)
    {
		echo "<script>parent.return_text('同名文件已经存在了')</script>";
        exit;
    }

    if(!move_uploaded_file ($filename, $destination))
    {
		echo "<script>parent.return_text('移动文件出错')</script>";
        exit;
    }

    $pinfo=pathinfo($destination);
    $fname=$pinfo['basename'];
	$url=$destination_folder.$fname;
	
	//关联的id，自由定义
	$id = $_POST['id'];
	echo '<script>parent.add("'.$url.'")</script>';

    
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['type']=='del_photo')
{
	$filepath = $_POST['name'];
	unlink($filepath); //删除文件 
}

/**
  * 修改一个图片 让其翻转指定度数
  * 
  * @param string  $filename 文件名（包括文件路径）
  * @param string  $src 输出文件名（包括文件路径）
  * @param  float $degrees 旋转度数 -90顺时针 90逆时针

  */
 function  flip($filename,$src,$degrees = 90)
 {
	  //读取图片
	  $data = @getimagesize($filename);
	  if($data==false)return false;
	  //读取旧图片
	  switch ($data[2]) {
	   case 1:
		$src_f = imagecreatefromgif($filename);break;
	   case 2:
		$src_f = imagecreatefromjpeg($filename);break;
	   case 3:
		$src_f = imagecreatefrompng($filename);break;
	  } 
	  if($src_f=="")return false;
	  $rotate = @imagerotate($src_f, $degrees,0);
	  if(!imagejpeg($rotate,$src,100))return false;
	  @imagedestroy($rotate);
	  return true;
 }
 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['type']=='turn_photo')
{
	$filepath = $_POST['name'];
	$degrees = $_POST['degrees'];
	flip($filepath,$filepath,$degrees);
} 

?>