<script language="php">
//header('Server: ');
//header('X-Powered-By: ');

$secchat=@parse_ini_file('secchat.ini');
if(!$secchat) die('���������������� ���� */secchat-classes/secchat.ini �����������!');


if ($secchat['force_https']==1)
{
	if ($_SERVER['SERVER_PORT']!=443) header('Location: https://'.$_SERVER['HTTP_HOST']);
	if ($_SERVER['HTTPS']!='on') die('�� �������� SSL!');
}


function nmysql()
{
global $secchat;

if ($secchat['is_persistent']==1)
    {
    $lnk=@mysql_pconnect($secchat['host'],$secchat['login'],$secchat['password']);
    }
else
    {
    $lnk=@mysql_connect($secchat['host'],$secchat['login'],$secchat['password']);
    }

if ($lnk)
    {
    mysql_select_db($secchat['database_name'],$lnk);
    return($lnk);
    }
    else
    {
	header('Content-Type: text/html; charset=windows-1251;');
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 
<head> 
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" /> 
<link rel="icon" href="/favicon.gif" type="image/gif" /> 
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" /> 
<META http-equiv="Content-Type" content="text/html; charset=windows-1251" /> 
<link href="/style.css" rel="stylesheet" type="text/css" /> 
<title>������! - ��� ���������� � ����� ������ MySQL!</title> 
</head> 
<body> 
<div id="wrap"> 
<div id="header"> 
	<h1>������!</h1> 
	<h2>��� ���������� � ����� ������ MySQL</h2> 
</div> 
 
<div id="content"> 
<p>���������� ���������� ���������� � ����� ������! ������� ��������� ������� � MySQL ���� ������ � �����<strong> /secchat-classes/secchat.ini</strong>!</p>
<p>����� ������� ���������, ��� ���� ��������������� ���� ����...</p>
<pre style="background-color:#999999">
[Settings for connection to databasae]
host=[IP ����� ����������, �� ������� �������� MySQL ������]
login=[����� ��� ������� � ���� ������]
password=[������ ��� ������� � ���� ������]
database_name=[�������� ���� ������]
is_persistent=0 

;1 - ���� ������� � Apache/nginx+PHP � MySQL ��������� �� ������ �������
;0 - ���� ��� ������� �� ����� ������

[General security settings]
force_https=1
; 0 - ��������� �������������� ���������� �� ��������� http (�� �������������)
; 1 - ��������� ������ ������������� ���������� �� ��������� https (�������������)




</pre>

</div> 
<div id="footer"> 
&copy; <a href="https://github.com/vodolaz095/secchat">SecChat</a>  - ��������� �������� - 2011 ���
</div> 
<div id="security" align="center">

<? 
echo '<p>����� ������� <strong id="server_time">'.date('H:i:s').'</strong>. ��� IP ����� <a href="https://www.nic.ru/whois/?query='.$_SERVER['REMOTE_ADDR'].'">'.$_SERVER['REMOTE_ADDR'].'</a>';
echo '. �������� HTTPS ������� ����� ���� '.$_SERVER['SERVER_PORT'].'</p>';
?>
</div>
</body> 
</html> 
<?	
exit();
	}
}

include ("class_user.php");
include ("class_page.php");
include ("class_channel.php");

</script>