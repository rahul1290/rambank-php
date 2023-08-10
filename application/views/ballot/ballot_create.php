<form name="f1" method="post" action="<?php echo base_url('ballot/add_user')?>">
    <table>
        <tr>
            <td>TokenId :</td>
            <td><input type="text" name="token" /></td>
        </tr>
        <tr>
            <td>Name :</td>
            <td><input type="text" name="name" /></td>
        </tr>
        <tr>
            <td>Address :</td>
            <td>
            <textarea name="address"></textarea></td>
        </tr>
        <tr>
            <td colspan="2" text-align="center"><input type="submit" name="submit" /></td>
        </tr>
    </table>
</form>