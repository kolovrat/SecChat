<script language="php">
if($_POST['s']==session_id())
	{
	if($_POST['new_pwd_val']==$_POST['new_pwd_val1'])	
		{
		$q='UPDATE users SET U_pwd="'.md5($this->filter_txt($_POST['new_pwd_val'])).'" WHERE UID="'.$this->rights['UID'].'"';
		mysql_query($q,$this->lnk);
		$_SESSION['U_pwd']=md5(session_id().md5($_POST['new_pwd_val']));
		?>
		<p>������ ������� ������!</p>
		<?		
		}
	else
		{
		?>
		<p><strong>������!</strong> ������ �� ���������!</p>
		<?
		}
	
	}
?>
<h1>������� ������</h1>
<form action="/change_pwd" method="post">
<input name="s" type="hidden" value="<? echo session_id(); ?>">
<p>������� ����� ������</p>
<p><input name="new_pwd_val" type="password"></p>
<p>�������� ����� ������ ��� 1 ���</p>
<p><input name="new_pwd_val1" type="password"></p>
<p><input name="" type="submit" value="������� ������"></p>
</form>
<?
</script>