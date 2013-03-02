<?php
if (stripos("win",PHP_OS) != false) {  
    echo "不支持Windows服务器!";  
  exit(1);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>MCloud - Make a cloud for you</title>
</head>

<body>
[MCloud 云管理系统]
<br />
<a href="#shuoming"><font color="#000000"><span style="text-decoration: none">[说明]</span></font></a>
<hr />
<?php
function deldir($dir){
  $dh = opendir($dir);
  while ($file = readdir($dh)){
  	if ($file != "." && $file != ".."){
  		$fullpath = $dir . "/" . $file;
  		if (!is_dir($fullpath)){
  			unlink($fullpath);
  		}else{
  			deldir($fullpath);
  		}
  	}
  }
  closedir($dh);
  if (rmdir($dir)){
  	return true;
  }else{
  	return false;
  }
}
//if(isset($_GET['file']) && $_GET['file'] != ""){
  if($_GET['command'] == "change"){
  	if(stripos($_GET['file'],"..") != false){
  		echo "你没有权利访问父文件夹";
  		exit(2);
  	}
  	if(isset($_GET['chd']) && $_GET['chd'] != ""){
  		echo "权限更改：";
  		if(chmod("./".$_GET['file'],$_GET['chd'])){
  			echo "权限更改成功！";
  		}else{
  			echo "权限更改失败，请确定输入的是权限值，并且您有权利访问此项目";
  		}
  		echo "<br />";
  		echo "<a href='?file=".$_GET['file']."'>返回</a>";
  		echo "<br />";
  	}else{
?>
<form method="get">
  属性值：<input name="chd" type="text" id="chd" size="4" maxlength="4" />
  <input name="file" type="hidden" id="file" value="<?php echo $_GET['file'] ?>" />
  <input name="command" type="hidden" id="command" value="change" />
  <input type="submit" name="submit" id="submit" value="提交" />
  <br />
  [例如： 0777 0755 ... 提示：如果权限修改不正确将会导致文件夹无法访问，此时必须再修改权限]
</form>
<p>
  <?
  	}
  }else if($_GET['command'] == "del"){
  	if(stripos($_GET['file'],"..") != false){
  		echo "你没有权利访问父文件夹";
  		exit(2);
  	}
  	if(file_exists("./".$_GET['file'])){
  		if(is_dir("./".$_GET['file'])){
  			if(deldir("./".$_GET['file'])){
  				echo "文件夹删除成功！";
  			}else{
  				echo "文件夹删除失败！";
  			}
  		}else{
  			if(unlink("./".$_GET['file'])){
  				echo "文件删除成功！";
  			}else{
  				echo "文件删除失败！";
  			}
  		}
  	}else{
  		echo "文件不存在";
  	}
  }else if($_GET['command'] == "upload"){
  	if(stripos($_GET['file'],"..") != false){
  		echo "你没有权利访问父文件夹";
  		exit(2);
  	}
  	if(file_exists("./".$_GET['file'])){
  		if(is_dir("./".$_GET['file'])){
  			set_time_limit(0);
  			echo "上传文件";
  			if($_FILES["file"]["type"] != ""){
  				if ($_FILES["file"]["error"] > 0)
  				{
  					echo "上传错误！错误号" . $_FILES["file"]["error"] . "<br />";
  				}else{
  					echo "文件名： " . $_FILES["file"]["name"] . "<br />";
  					echo "MIME类型： " . $_FILES["file"]["type"] . "<br />";
  					echo "文件大小： ";
  					if($_FILES["file"]["size"] / 1024 < 1024){
  						echo ($_FILES["file"]["size"] / 1024) . " Kb<br />";
  					}else if($_FILES["file"]["size"] / 1048576 < 1024){
  						echo ($_FILES["file"]["size"] / 1048576) . " Mb<br />";
  					}else{
  						echo ($_FILES["file"]["size"] / 1073741824) . " Gb<br />";
  					}
  					if (file_exists("upload/" . $_FILES["file"]["name"])){
  						echo $_FILES["file"]["name"] . "已经存在";	
  					}else{
  						move_uploaded_file($_FILES["file"]["tmp_name"],"./".$_GET['file']. $_FILES["file"]["name"]);
  						echo "文件位置：".$_GET['file'].$_FILES["file"]["name"];
  					}
  					echo "<br />";
  				}
  			}else{
  				echo "最大上传大小：".ini_get('post_max_size')."<br />";
?>
<form method="post" enctype="multipart/form-data">
<label>文件:</label>
<input type="file" name="file" id="file" /> 
<input name="file" type="hidden" id="file" value="<?php echo $_GET['file'] ?>" />
<input name="command" type="hidden" id="command" value="upload" />
<input type="submit" name="submit" value="上传" />
</form>
<?
  			}
  		}else{
  			echo "非文件夹";
  		}
  	}else{
  		echo "文件不存在";
  	}
  }else{
  	if(stripos($_GET['file'],"..") != false){
  		echo "你没有权利访问父文件夹";
  		exit(2);
  	}
  	if(file_exists("./".$_GET['file'])){
  		if(is_dir("./".$_GET['file'])){
  			exec("ls ./".$_GET['file'],$out,$status);
  			if($status == 0){
  				echo "目录内文件：";
  				echo "<br />";
  				for($i=0;$i<count($out);$i++){
  					echo "<a href='?file=".$_GET['file']."/".$out[$i]."'>";
  					echo $out[$i];
  					echo "</a>";
  					echo "<br />";
  				}
  				if(count($out) == 0){
  					echo "文件夹里没有文件";
  				}else{
  					echo "请选择需要的文件";
  				}
  			}else{
  				echo "查看文件夹内文件错误！无法访问本文件夹！错误号：".$status."。";
  			}
  			echo "<br />";
  			echo "<a href='?file=".$_GET['file']."&command=change"."'>更改文件夹权限</a>";
  			echo "<br />";
  			echo "<a href='?file=".$_GET['file']."&command=del"."'>删除（不可取回）</a>";
  			echo "<br />";
  			echo "<a href='?file=".$_GET['file']."&command=upload'>上传文件</a>";
  			echo "<br />";
  		}else{
  			$filenamearr = explode(".",$_GET['file']);
  			$filenamerarr = explode("/",$_GET['file']);
  			echo "文件名：".$filenamerarr[count($filenamerarr)-1];
  			echo "<br />";
  			echo $filenamearr[count($filenamearr)-1]."文件";
  			echo "<br />";
  			echo "<a href='".$_GET['file']."' >访问</a>";
  			echo "<br />";
  			echo "<a href='?file=".$_GET['file']."&command=change"."'>更改文件权限</a>";
  			echo "<br />";
  			echo "<a href='?file=".$_GET['file']."&command=del"."'>删除（不可取回）</a>";
  			echo "<br />";
  		}
  	}else{
  		echo "文件不存在！";
  	}
  }
?>
<a href="?file=/"><font color="#000000"><span style="text-decoration: none">返回根目录</span></font></a>
<?
/*}else{
  exec("ls",$out,$status);
  if($status == 0){
  	echo "目录内文件：";
  	echo "<br />";
  	for($i=0;$i<count($out);$i++){
  		echo "<a href='?file=".$out[$i]."'>";
  		echo $out[$i];
  		echo "</a>";
  		echo "<br />";
  	}
  	echo "请选择需要的文件";
  }else{
  	echo "查看文件夹内文件错误！无法访问根目录！错误号：".$status."。";
  }
  echo "<br />";
  echo "<a href='?file=/&command=upload'>上传文件</a>";
}*/
?>
<hr />
  <a name="shuoming" id="shuoming"></a>
  说明：
  <br />
</p>
<center>
  <p>本软件由TLL制作，使用php语言，虽然简陋，但是达只用一个文件就可以达到管理效果，谢谢使用~ </p>
  <p>上传文件的大小限制请看服务器php.ini的设置<br />
  	E-mail: 1040424979@qq.com QQ:1040424979
  <br />
  	Mcloud [Version 1.0 简体中文版]	</p>
</center>
</body>
</html>
