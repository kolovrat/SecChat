<script language="php">
if ($_POST['logoff']==1)
	{
	session_destroy();
	header("Location: /");
	setcookie('cl',false);
	setcookie('cp',false);			
	}

class USER
{
private $lnk;
private $user;
private $error;
////////////////////
public function get_user()
	{
	if ($this->user)
		{
		$ans=$this->user;
		$ans['U_pwd']='***';
		}
		else $ans=false;

	return $ans;
	}

public function get_error()
{
	return $this->error;
}
////////////////////////////////////
private function filter($a)
	{
	if (is_numeric($a)) return $a;
	else return false;
	}

private function filter_txt($a)
	{
	$a=trim($a);
	$a=stripslashes($a);
	$a=mysql_real_escape_string($a);		
	return $a;	
	}
////////////////////////////////////

public function test()
{
var_dump($this);
echo '<hr>POST';
var_dump($_POST);
echo '<hr>SESSION';
var_dump($_SESSION);
echo '<hr>cookie ';
var_dump ($_COOKIE);
}

public function __construct($link)
{
//*****
if (mysql_ping($link) and session_id())
{
$this->lnk=$link;
	
	if (!isset($_SESSION['UA'])) $_SESSION['UA']=md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);		
	
	if($_POST['UL'] and $_POST['UP'] and $_POST['ssu']==session_id())
		{
			$_SESSION['U_login']=$_POST['UL'];
			$_SESSION['U_pwd']=md5(session_id().md5($_POST['UP']));
			$log=true;		
		}
	

	if (isset($_SESSION['U_login']) and isset($_SESSION['U_pwd']))
	{
	
	if (md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'])==$_SESSION['UA'])
		{
		$qqq='SELECT * FROM  users  WHERE U_login="'.$this->filter_txt($_SESSION['U_login']).'" && MD5(CONCAT("'.session_id().'",U_pwd))="'.$this->filter_txt($_SESSION['U_pwd']).'" && U_active=1';
		//echo $qqq;
		$res=mysql_query($qqq,$this->lnk);
		echo mysql_error($this->lnk);
		//var_dump($res);
		if (mysql_num_rows($res)==1)
			{
			$this->user=mysql_fetch_assoc($res);
			$this->user['U_pwd']='***';

			if ($log) mysql_query('INSERT INTO user_iplog(login,IP,status,useragent) VALUES ("'.$this->filter_txt($_SESSION['U_login']).'","'.$_SERVER['REMOTE_ADDR'].'","0","'.$_SERVER['HTTP_USER_AGENT'].'")',$this->lnk);
			unset($log);
			$this->error=false;
			return $this;
			}
			else
			{
			if ($log) mysql_query('INSERT INTO user_iplog(login,IP,status,useragent) VALUES ("'.$this->filter_txt($_SESSION['U_login']).'","'.$_SERVER['REMOTE_ADDR'].'","1","'.$_SERVER['HTTP_USER_AGENT'].'")',$this->lnk);
			unset($log);
			
			$this->user=false;
			$this->error="�� ��������� ����� ��� ������ ������������!";
			session_destroy();			
			return false;
			}
		
		}
		else
		{
		$this->error="����� ������?";
		mysql_query('INSERT INTO user_iplog(login,IP,status,useragent) VALUES ("'.$this->filter_txt($_SESSION['crewLOGIN']).'","'.$_SERVER['REMOTE_ADDR'].'","3","'.$_SERVER['HTTP_USER_AGENT'].'")',$this->lnk);
		$this->user=false;
		session_destroy();
		return(false);
		}

	}


}
else
{
	die('�� ����� ��������� PHP ������ ��� �� ��� ���������� � ����� ������ MySQL!');
}

//*****
}

public function ip_log()
{
if ($this->get_user())
	{
$res=mysql_query('SELECT * FROM user_iplog WHERE login="'.$this->user['U_login'].'" ORDER BY DTS DESC',$this->lnk);
$b=mysql_num_rows($res);
?>
<h1>������ � ������ ����������</h1>
<table border="1" cellpadding="1" cellspacing="0" align="center">
<tr align="center">
<td>�����</td>
<td>�����</td>
<td>IP</td>
<td>���������</td>
<td>��������� ������� �������</td>
</tr>
<?

for ($i=0;$i<$b;$i++)
	{
	echo '
	<tr align="center">
	<td>'.date('j M y',strtotime(mysql_result($res,$i,'DTS'))).'  /  '.date('H:i:s',strtotime(mysql_result($res,$i,'DTS'))).'</td>
	<td>'.mysql_result($res,$i,'login').'</td>
	<td><a href="https://www.nic.ru/whois/?query='.mysql_result($res,$i,'IP').'">'.mysql_result($res,$i,'IP').'</a></td>';
	
	if (mysql_result($res,$i,'status')==0) echo '<td  bgcolor="#33FF33">�������</td>';
	elseif (mysql_result($res,$i,'status')==1) echo '<td  bgcolor="#999999">������ ������</td>';
	elseif (mysql_result($res,$i,'status')==2) echo '<td  bgcolor="#FFFF00">����������� IP �����.</td>';
	elseif (mysql_result($res,$i,'status')==3) echo '<td  bgcolor="#FF0000">����� ������</td>';
	else echo '<td bgcolor="#F9595E">�����?</td>';
	
	echo '<td><textarea name="" cols="40" rows="3" disabled="disabled">'.mysql_result($res,$i,'useragent').'</textarea></td>';
	echo '</tr>';

	}
echo '</table>';			
?>
<h3>������</h3>
<p>�� ���� �������� ����� ������ � ��������� ������� � ������ ���������� �����. � ������� ������������ �������� ����� �������������, IP ������, ��������� �������� � ���������� ������������ ������ ���������� � ����� ��������� ������� � ������ ����������.</p>
<p>�������� ��������� ����������� ������� �������������</p>
<ul>
<li>"�������" - ������������ ��� ���������� ��� � ������, ��� ���� �� �������� � �������� � ������������ IP ������.</li>
<li>"������ ������" - ������������ ��� �� ���������� ���� ����� � ������.</li>
<li>"����� ������" - ������������ ������� ������� ������������� ������ ����������������� ������������. � ����  ������ ������������� ��������� ��������� ������������� ������������  �� ������, � �������� IP ����� ������������� � ������������������ ������. � �������, ���� ��� ������� ����� �������������� ������ ������ ������������ �� ����� ������� �������������!</li>
</ul><?
	}
}

////////////////////////end class
}
</script>