<script language="php">
class PAGE
{
private $lnk;
private $USER;
private $rights;
private $template;
////////////
public function filter($a)
{
if (is_numeric($a))
return mysql_real_escape_string($a);
else
return false;
}

public function filter_txt($a)
{
$a=trim($a);
$a=mysql_real_escape_string($a);	
return $a;
}



////////////
public function __construct($user,$link)
	{
	if (mysql_ping($link)  and get_class($user)=='USER')
		{
		$this->lnk=$link;
		$this->USER=$user;
		$this->rights=$this->USER->get_user();
		/*
		 * ������ ���������
		 */
		}
		else
		{
		die("No connection to db");
		}
	}

private function makeheader($title,$subtitle)
{
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 
<head> 
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" /> 
<link rel="icon" href="/favicon.gif" type="image/gif" /> 
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" /> 
<META http-equiv="Content-Type" content="text/html; charset=windows-1251" /> 
<link href="/style.css" rel="stylesheet" type="text/css" /> 
<title><? echo $title;?></title> 
<script language="javascript" src="/jquery.min.js" type="text/javascript"></script> 
<script language="javascript" src="/ajax.js" type="text/javascript"></script> 
</head> 
<body> 
<div id="wrap"> 
<div id="header"> 
	<h1><? echo $title;?></h1> 
	<h2><? echo $subtitle;?></h2> 
</div> 
 
<div id="nav"> 
	<ul> 
		<li><a href="/">�������</a></li> 
		<li><a href="/about">� �������</a></li> 
<?		
if ($this->rights) 
{
if ($this->rights['admin_users'] or $this->rights['moder_users']) echo '		<li><a href="/admin_users">������������</a></li>';

if ($this->rights['admin_channels'] or $this->rights['moder_channels']) echo '		<li><a href="/admin_channels">������������� ������</a></li>';

if ($this->rights['admin_channels']==1 and $this->rights['admin_users']==1)  echo '		<li><a href="/panic">�������</a></li>';
echo '		<li><a href="/log">�������� �������</a></li>';
echo '		<li><a href="/exit">�����</a></li>';
}
?>
	</ul> 
</div> 
 
<div id="content"> 
<?


}

private function makebottom()
{
?>
</div> 
<div id="footer"> 
&copy; SecChat  - ��������� �������� - 2011 ���
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
}

private function mainpage()
{
	if ($this->rights)
	{
	$this->makeheader('����������� ��������','����� ����������, '.$this->rights['U_login'].'!');
	?><h3>���� ������</h3><?
	$res=mysql_query('SELECT * FROM channels WHERE channel_admin_UID="'.$this->filter($this->rights['UID']).'"',$this->lnk);
	$b=mysql_num_rows($res);
	if ($b)
	{
	for($i=0;$i<$b;$i++)
		{
		echo '<p><strong><a href="/c/'.mysql_result($res,$i,'channel_name').'">'.mysql_result($res,$i,'channel_name').'</a></strong> - ';
		echo ''.mysql_result($res,$i,'channel_mesg').'</p>';
		}
	}
	else
	{
	echo '<p>��� �������</p>';
	}
	
	?><h3>������, �� ������� �� ���������</h3><?
	$res=mysql_query('SELECT * FROM c_u LEFT JOIN channels ON (c_u_channel=channel_id) WHERE c_u_UID="'.$this->filter($this->rights['UID']).'" && channel_admin_UID!="'.$this->filter($this->rights['UID']).'"',$this->lnk);
	$b=mysql_num_rows($res);
	if ($b)
	{
	for($i=0;$i<$b;$i++)
		{
		echo '<p><strong><a href="/c/'.mysql_result($res,$i,'channel_name').'">'.mysql_result($res,$i,'channel_name').'</a></strong> - ';
		echo ''.mysql_result($res,$i,'channel_mesg').'</p>';
		}
	}
	else
	{
	echo '<p>��� �������</p>';
	}
	
	$this->makebottom();
	}
else
	{
	$this->makeheader('�����������','');
	?>
	<div class="msg" align="center">
	<form action="" method="post">
	<input name="ssu" type="hidden" value="<? echo session_id();?>" />
	<p><input name="UL" type="text" /></p>
	<p><input name="UP" type="password" /></p>
	<p><input type="submit" value="OK" />
	</form>
	</div>
	<?	
	$this->makebottom();
	}
return true;
}

private function show_channel($a)
{
$c=new CHANNEL($a,$this->USER,$this->lnk);
if ($c)
	{
	$this->makeheader('�����: '.$a,'');
	$c->show();
	$this->makebottom();
	}
else
	{
	header("HTTP/1.0 404 Not Found");
	}
return true;
}

private function iplog()
{
	$this->makeheader('��� �������','');
	$this->USER->ip_log();
	$this->makebottom();
}

private function about()
{
	$this->makeheader('� ������� SecChat','');
	echo '������, 3 �����!';
	$this->makebottom();
}

private function admin_users()
{
	$this->makeheader('������������� �������������','');
	if ($this->rights['admin_users']==1) $s=2;
	elseif ($this->rights['moder_users']==1)  $s=1;
	else  $s=0;


	if($s>0)
	{
	if ($_POST['s']==session_id())
		{
		if ($_POST['unmake_admin_users'] and $this->rights['admin_users']==1)
			{
			$qqq='UPDATE users SET admin_users=0 WHERE UID="'.$this->filter($_POST['unmake_admin_users']).'"';
			mysql_query($qqq,$this->lnk);
			}

		if ($_POST['make_admin_users'] and $this->rights['admin_users']==1)
			mysql_query('UPDATE users SET admin_users=1 WHERE UID="'.$this->filter($_POST['make_admin_users']).'"',$this->lnk);

		if ($_POST['unmake_moder_users'] and $this->rights['admin_users']==1)
			mysql_query('UPDATE users SET moder_users=0 WHERE UID="'.$this->filter($_POST['unmake_moder_users']).'"',$this->lnk);

		if ($_POST['make_moder_users'] and $this->rights['admin_users']==1)
			mysql_query('UPDATE users SET moder_users=1 WHERE UID="'.$this->filter($_POST['make_moder_users']).'"',$this->lnk);

		if ($_POST['make_admin_channels'] and $this->rights['admin_users']==1)
			mysql_query('UPDATE users SET admin_channels=1 WHERE UID="'.$this->filter($_POST['make_admin_channels']).'"',$this->lnk);

		if ($_POST['unmake_admin_channels'] and $this->rights['admin_users']==1)
			mysql_query('UPDATE users SET admin_channels=0 WHERE UID="'.$this->filter($_POST['unmake_admin_channels']).'"',$this->lnk);

		if ($_POST['make_moder_channels'] and $this->rights['admin_users']==1)
			mysql_query('UPDATE users SET moder_channels=1 WHERE UID="'.$this->filter($_POST['make_moder_channels']).'"',$this->lnk);

		if ($_POST['unmake_moder_channels'] and $this->rights['admin_users']==1)
			mysql_query('UPDATE users SET moder_channels=0 WHERE UID="'.$this->filter($_POST['unmake_moder_channels']).'"',$this->lnk);
			
			if ($_POST['make_active'] and $this->rights['admin_users']==1)
			mysql_query('UPDATE users SET U_active=1 WHERE UID="'.$this->filter($_POST['make_active']).'"',$this->lnk);

		if ($_POST['unmake_active'] and $this->rights['admin_users']==1)
			mysql_query('UPDATE users SET U_active=0 WHERE UID="'.$this->filter($_POST['unmake_active']).'"',$this->lnk);

		if ($_POST['create_new_user'])	
			{
			$pwd=strtoupper(substr((md5(date('c'))),0,8));
			echo '<p>������������ ������! ����� <strong>'.$_POST['create_new_user'].'</strong>. ������ <strong>'.$pwd.'</strong></p>';
			$qqq='INSERT INTO users(U_login,U_pwd) VALUES ("'.$this->filter_txt($_POST['create_new_user']).'","'.$pwd.'")';
//			echo $qqq;
			mysql_query($qqq,$this->lnk);
			}
			echo mysql_error($this->lnk);
		}
////////////////////////////	
	
	if ($s==2) $res=mysql_query('SELECT * FROM users WHERE UID!="'.$this->rights['UID'].'" ORDER BY U_login ASC',$this->lnk);	
	elseif ($s==1) $res=mysql_query('SELECT * FROM users WHERE U_host="'.$this->rights['UID'].'" ORDER BY U_login ASC',$this->lnk);	
	
	$d=mysql_num_rows($res);
	if($d)
		{

echo '		<table border="1" cellpadding="3" cellspacing="0" align="center">';
echo '		<tr>';
echo '		<td>���</td>';
echo '		<td>������</td>';
echo '		<td>�������</td>';


		if($s==2)
		{
		echo '		<td>���. �������������</td>';		
		echo '		<td>��������� �������������</td>';
		echo '		<td>���. �������</td>';
		echo '		<td>��������� �������</td>';				
		}
			 echo '		</tr>';

		for($i=0;$i<$d;$i++)		
			{

echo '		<tr align="center" valign="middle">';
echo '		<td><p>'.mysql_result($res,$i,'U_login').'</p></td>';
echo '		<td>';
?>
<form action="" method="post" name="new_pwdf">
<input name="s" value="<? echo session_id();?>" type="hidden" id="new_pwd_s">
<input name="new_pwd" value="<? echo mysql_result($res,$i,'UID');?>"  type="hidden">
<p><input name="new_pwd_val" type="password"><input name="" type="submit" value="������"></p>
</form>

<?
echo '</td>';
echo '<td>';
			if (mysql_result($res,$i,'U_active')==1)
			{
?>
<form action="" method="post">
<input name="s" value="<? echo session_id();?>" type="hidden">
<input name="unmake_active" value="<? echo mysql_result($res,$i,'UID');?>"  type="hidden">
<p><input name="" type="submit" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" class="button_green"></p>
</form>
<?
			}
			else
			{
?>
<form action="" method="post">
<input name="s" value="<? echo session_id();?>" type="hidden">
<input name="make_active" value="<? echo mysql_result($res,$i,'UID');?>"  type="hidden">
<p><input name="" type="submit" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" class="button_red"></p>
</form>
<?
			
			}
			echo '</td>';


		if($s==2) 
		{
			echo '<td>';
			if (mysql_result($res,$i,'admin_users')==1)
			{
?>
<form action="" method="post">
<input name="s" value="<? echo session_id();?>" type="hidden">
<input name="unmake_admin_users" value="<? echo mysql_result($res,$i,'UID');?>"  type="hidden">
<p><input name="" type="submit" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" class="button_green"></p>
</form>
<?
			}
			else
			{
?>
<form action="" method="post">
<input name="s" value="<? echo session_id();?>" type="hidden">
<input name="make_admin_users" value="<? echo mysql_result($res,$i,'UID');?>"  type="hidden">
<p><input name="" type="submit" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" class="button_red"></p>
</form>
<?
			
			}
			echo '</td>';
			echo '<td>';

			if (mysql_result($res,$i,'moder_users')==1)
			{
?>
<form action="" method="post">
<input name="s" value="<? echo session_id();?>" type="hidden">
<input name="unmake_moder_users" value="<? echo mysql_result($res,$i,'UID');?>"  type="hidden">
<p><input name="" type="submit" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" class="button_green"></p>
</form>
<?
			}
			else
			{
?>
<form action="" method="post">
<input name="s" value="<? echo session_id();?>" type="hidden">
<input name="make_moder_users" value="<? echo mysql_result($res,$i,'UID');?>"  type="hidden">
<p><input name="" type="submit" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" class="button_red"></p>
</form>
<?
			
			}
		echo '</td>';
		echo '<td>';
			if (mysql_result($res,$i,'admin_channels')==1)
			{
?>
<form action="" method="post">
<input name="s" value="<? echo session_id();?>" type="hidden">
<input name="unmake_admin_channels" value="<? echo mysql_result($res,$i,'UID');?>"  type="hidden">
<p><input name="" type="submit" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" class="button_green"></p>
</form>
<?
			}
			else
			{
?>
<form action="" method="post">
<input name="s" value="<? echo session_id();?>" type="hidden">
<input name="make_admin_channels" value="<? echo mysql_result($res,$i,'UID');?>"  type="hidden">
<p><input name="" type="submit" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" class="button_red"></p>
</form>
<?
			
			}
		echo '</td>';		
		echo '<td>';
			if (mysql_result($res,$i,'moder_channels')==1)
			{
?>
<form action="" method="post">
<input name="s" value="<? echo session_id();?>" type="hidden">
<input name="unmake_moder_channels" value="<? echo mysql_result($res,$i,'UID');?>"  type="hidden">
<p><input name="" type="submit" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" class="button_green"></p>
</form>
<?
			}
			else
			{
?>
<form action="" method="post">
<input name="s" value="<? echo session_id();?>" type="hidden">
<input name="make_moder_channels" value="<? echo mysql_result($res,$i,'UID');?>"  type="hidden">
<p><input name="" type="submit" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" class="button_red"></p>
</form>
<?
			
			}
			echo '</td>';
		}
				
		
			 echo '		</tr>';

			
			}
		echo '		</table>';
		}	
	
	}
	
	?>
	<h3>������� ������ ������������</h3>
	<form action="" method="post">
	<input name="s" type="hidden" value="<? echo session_id();?>">
	<p><input name="create_new_user" type="text"><input name="" type="submit" value="OK"></p>
	</form>
	<h3>������</h3>
	<p>�� ���� �������� ����� ������������� �������������, ������� �� ������� ���� ��- ��������� �������������, ��� �� ���� �������������, ���� �� - ������������� �������������.</p>
	<p>�������� ���� �������������</p>
	<ul>
	<li><strong>�������</strong> - ������������ ����� �������������� �� �����</li>
	<li><strong>������������� �������������</strong> - ������������  ����� ������ ����� ���� ������ �������������, � ����� ��������� �����.</li>
	<li><strong>��������� �������������</strong> - ������������ ����� ��������� ����� ������������� � ����������� ������� ����, ������������  �������������, � ������ �� ������. ��������� ����� ������ ��������� ������� ��� �������������, ������� �� ������!</li>	
	<li><strong>������������� �������</strong> - ����� ��������� ����� ������, ������������� ��� ������, ����������� �� ��� �������������.</li>	
	<li><strong>��������� �������</strong> - ����� ��������� ����� ������, ������������� ���� ������, ����������� �� ��� �������������.</li>
	</ul>
	<?
	
	
		
	$this->makebottom();
}

private function admin_channels()
{
	if ($this->rights['admin_channels']==1) 
	$qqq='SELECT *,COUNT(c_u_id) AS num_users FROM channels 
	LEFT JOIN c_u ON (c_u_channel=channel_id) 
	LEFT JOIN users ON (UID=channel_admin_UID) 
	GROUP BY channel_id ORDER BY channel_name ASC';

	elseif ($this->rights['moder_channels']==1)
	$qqq='SELECT *,COUNT(c_u_id) AS num_users FROM channels 
	LEFT JOIN c_u ON (c_u_channel=channel_id) 
	LEFT JOIN users ON (UID=channel_admin_UID) 
	WHERE channel_admin_UID="'.$this->rights['UID'].'"
	GROUP BY channel_id ORDER BY channel_name ASC';
	else die('error 580');

	$this->makeheader('������������� ������','');
	
		if($_POST['s']==session_id())
		{
		if($_POST['edit']) {echo 'edit';}
		if($_POST['new']) {echo 'edit';}
		if($_POST['del']) {echo 'edit';}		
		}

	
	$res=mysql_query($qqq,$this->lnk);
	$b=mysql_num_rows($res);
	if ($b)
	{
	?>
	<table border="1" cellpadding="3" cellspacing="0" width="90%">
	<tr>
	<td>��������</td>
	<td>��������</td>
	<td>�������������</td>
	<td>���������� �������������</td>
	</tr>
	<?
	for($i=0;$i<$b;$i++)
		{
	echo '<tr>';
	echo '<td><a href="/c/'.mysql_result($res,$i,'channel_name').'">'.mysql_result($res,$i,'channel_name').'</a></td>';
	echo '<td>'.mysql_result($res,$i,'channel_mesg').'</td>';
	echo '<td>'.mysql_result($res,$i,'U_login').'</td>';
	echo '<td align="center">'.mysql_result($res,$i,'num_users').'</td>';
	echo '</tr>';
		}
	echo '</table>';
	}

	
	$this->makebottom();
}

private function panic()
{
	if ($this->rights['admin_channels']==1 and $this->rights['admin_users']==1)
	{
		$this->makeheader('������� ���� ������','');
		if($_POST['s']==session_id())
		{
		if($_POST['channels']) {echo '<p>������ �������!</p>';mysql_query('TRUNCATE channels',$this->lnk);}
		if($_POST['c_u']) {echo '<p>�������� � ������� ��������!</p>';mysql_query('TRUNCATE c_u',$this->lnk);}		
		if($_POST['mesg']) {echo '<p>��������� �������!</p>';mysql_query('TRUNCATE mesg',$this->lnk);}
		if($_POST['users']) {echo '<p>������������ �������!</p>';mysql_query('TRUNCATE users',$this->lnk);}
		if($_POST['user_iplog']) {echo '<p>��� ������� ������!</p>';mysql_query('TRUNCATE user_iplog',$this->lnk);}						
		}
		?>
		<p>�� ����������� �������� ���� ������, �������� ��� ���� �����. ��������, ��� �������.</p>
		<form action="/panic" method="post"><input name="s" type="hidden" value="<? echo session_id();?>">
		<p><input name="channels" type="checkbox" value="1">������</p>
		<p><input name="c_u" type="checkbox" value="1">�������� � �������</p>
		<p><input name="mesg" type="checkbox" value="1">���������</p>
		<p><input name="users" type="checkbox" value="1">�������������</p>	
		<p><input name="user_iplog" type="checkbox" value="1">�������� ������� �������������</p>
		<p>������������� <input name="confirm" type="text"> (�������� "DELETE").</p>
		<input name="" type="submit" value="�������!">	
		</form>
		<p><strong>��������!</strong> ������ ��� �������� ����� ������������ � ������� ����� ����������, 
		�� ����� �������, ������� ������� ���� ������ mysql, ����� ����� ��������� ���������� �������� ����������.</p>
		<?
		$this->makebottom();
	}
	else header("HTTP/1.0 404 Not Found");
}

public function dispatcher($q)
{
//var_dump($q);
if($q=='/') $this->mainpage();
elseif(preg_match('~^/c/([A-z0-9_\-]+)~',$q,$a) and $this->rights) $this->show_channel($a[1]);
elseif(preg_match('~^/log/?~',$q) and $this->rights) $this->iplog();
elseif(preg_match('~^/about/?~',$q)) $this->about();
elseif(preg_match('~^/admin_users/?~',$q) and ($this->rights['admin_users']==1 or $this->rights['moder_users']==1) ) $this->admin_users();
elseif(preg_match('~^/admin_channels/?~',$q)  and ($this->rights['admin_channels'] or $this->rights['moder_channels'])) $this->admin_channels();
elseif(preg_match('~^/exit/?~',$q)) {session_destroy();header("Location: /");}
elseif(preg_match('~^/panic/?~',$q) and $this->rights['admin_users']==1 and $this->rights['admin_channels']==1) $this->panic();
else header("HTTP/1.0 404 Not Found");
}
}
</script>