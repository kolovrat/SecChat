<script language="php">

	$this->makeheader('������������� �������������','');
	if ($this->rights['admin_users']==1) $s=2;
	elseif ($this->rights['moder_users']==1)  $s=1;
	else  $s=0;

	if ($s>0)
	{
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
	}
	else
	{
	?>
	<p>� ��� ����������� ���� ��� �������������� �������������!</p>	
	<?
	}
	
		
	$this->makebottom();
</script>