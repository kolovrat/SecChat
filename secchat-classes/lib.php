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
    die('���������� ���������� ���������� � ����� ������! ������� ��������� ������� � MySQL ���� ������ � ����� /secchat-classes/secchat.ini!');
    }
}

include ("class_user.php");
include ("class_page.php");
include ("class_channel.php");

</script>