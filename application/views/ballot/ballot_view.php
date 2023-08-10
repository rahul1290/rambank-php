<a href="<?php echo base_url('ballot/user_add')?>">Create New User</a>
<table border="1">
    <?php 
        foreach($result as $r){ ?>
           <tr>
               <td><input type="checkbox" class="checkbox" data-id="<?php echo $r['id']; ?>" <?php if($r['is_winner']==1){ echo 'checked';}?>/></td>
               <td><?php echo $r['token']; ?></td>
               <td><?php echo $r['name']; ?></td>
               <td><?php echo $r['address']; ?></td>
               <td><a href="<?php echo base_url('ballot/delete_user/'.$r['id']);?>">Delete</a></td>
           </tr> 
    <?php  }
    ?>
</table>

<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script>
    $(document).ready(function(){
        $(".checkbox").change(function() {
            const id = $(this).data('id')
            setWinner(id)
        });
        
    })
    
    const setWinner = async(id) => {
        const CandidateData = await fetch('http://gyanodayvidyaniketan.com/rambank/ballot/set_winner/'+id)
        alert('New winner set.')
        location.reload();
    }
</script>