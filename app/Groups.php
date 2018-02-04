<?php
class Groups {

    public static function HTMLtop($user) {
?>
      <div class="container">
        <h2>Welcome, <?= $user->getCRSID(); ?></h2>
        <p>
<?
          if(!$user->isIndividual()){
            $owner = $user->ownsGroup($user->getGroupId());
?>
            You are currently <?= $owner ? "owner" : "part" ?> of the group <a href='?view=<?= $user->getGroupId(); ?>'>"<?= $user->getEscapedGroupName(); ?>"</a><br />
<?
            if(!$owner){
?>
              <a href='?leave'>Leave this Group</a><br />
<?
            }
          }else{
?>
            You are currently balloting alone.<br />
<?
          }

          if($user->getRequestingGroupId()){ ?>
            <a href='?view=<?= $user->getRequestingGroupId(); ?>'>You are currently requesting access to a group</a><br />
<?
          }
?>
          <a href='?create'>Create a new Group</a>
        </p>
<?php
    }

    public static function maxGroupSize(){
      return 9;
    }

    public static function getGroupQuery($id){
      if(is_numeric($id)){
        return "SELECT `ballot_individuals`.`id` as `id`, `groupid`, `name` as 'groupname', `crsid` FROM `ballot_groups` JOIN `ballot_individuals` ON `groupid`=`ballot_groups`.`id` WHERE `ballot_groups`.`id`='$id'";
      }else{
        return "";
      }
    }

    public static function HTMLgroupView($user, $result){
      if($result->num_rows == 0){ ?>
        <h2>No group found</h2>       
<?
      }
      $first = true;
      while($row = $result->fetch_assoc()){
        if($first){ 
          $owner = $user->ownsGroup($row['groupid']);
          $first = false; ?>
          <h2><?= htmlentities($row['groupname']); ?></h2> 
<?
          //Only show request link if not currently in the group, or requesting access
          if($user->getGroupId() != intval($row['groupid']) && $user->getRequestingGroupId() != intval($row['groupid'])){
?>
            <a href='/groups?join&id=<?= $row['groupid'] ?>'>Request to Join</a>
<?
          }else if($owner){
?>
            You are owner of this group.
<?
          }
?>
          <h3>Members</h3>
          <table class="table table-condensed table-bordered table-hover">
            <thead>
              <tr>
                <td>CRSid</td>
<?              if($owner){ ?>
                  <td>Assign Ownership</td>
<?              } ?>
              </tr>
            </thead>
            <tr>
              <td><?= $row['crsid']; ?></td>
<?            if($owner){
                if($user->getCRSID() == $row['crsid']) {?>
                  <td></td>
<?              }else{ ?>
                  <td><a href='?assign=<?= $row['id']; ?>&group=<?= $row['groupid']; ?>'>Assign Ownership</a></td>
<?              }
              }?>
            </tr>
<?
        }else{ ?>
          <tr>
            <td><?= $row['crsid']; ?></td>
<?            if($owner){
                if($user->getCRSID() == $row['crsid']) {?>
                  <td></td>
<?              }else{ ?>
                  <td><a href='?assign=<?= $row['id']; ?>&group=<?= $row['groupid']; ?>'>Assign Ownership</a></td>
<?              }
              }?>
          </tr>
<?
        }
      } ?>
      </table>
<?
    }
    public static function HTMLjoin(){ ?>

<?
    }

    public static function HTMLcreate(){ ?>
      <h2>Create a New Group</h2>
      <form action="" method="POST">
        <input name="groupname" type="text" placeholder="Group Name" /><br />
        <label for="public">Make this group visible publically?</label> <input name="public" type="checkbox" value="false" /><br />
        <input type="submit" value="Create Group" />
      </form>
<?
    }

    public static function HTMLgroupList($result, $user = null){
        if($result->num_rows > 0){ ?>
          <h2>Public Groups</h2>
          <table class="table table-condensed table-bordered table-hover" >
            <thead>
              <tr>
                <td>Name</td>
                <td>Size</td>
                <td>Request</td>
              </tr>
            </thead>
<?
            if($user != null){
              if(!$user->isIndividual()){ ?>
                <tr class='current-group'>
                  <td><a href='/groups?view=<?= $user->getGroupId(); ?>'><?= $user->getEscapedGroupName(); ?></a></td>
                  <td><?= $user->getGroupSize(); ?></td>
                  <td><a href='?leave'>Leave this group</a></td>
                </tr>
<?
              }

              if($user->getRequestingGroupId() != null){ 
                $groupData = $user->getRequestingGroup(); ?>
                <tr class='current-request'>
                  <td><a href='/groups?view=<?= $groupData['id'] ?>'><?= htmlentities($groupData['name']); ?></a></td>
                  <td><?= $groupData['size']; ?></td>
                  <td><a href='?cancel'>Cancel join request</a></td>
                </tr>
<?
              }
            }
            while ($row = $result->fetch_assoc()) {
?>
              <tr>
                <td><a href='/groups?view=<?= $row['id'] ?>'><?= htmlentities($row['name']); ?></a></td>
                <td><?= $row['size']; ?></td>
                <td><a href='/groups?join&id=<?= $row['id']; ?>'>Request to Join</a></td>
              </tr>
<?
            }
?>
          </table>
<?
        }
    }

    public static function HTMLbottom() {
?>
      </div>
<?php
	}
}

?>
