<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-type');
header('Content-Type: application/json');

class vueAPI {
    private $input, $con;
    function __construct() {
        // Fetch incoming data
        $input = json_decode(file_get_contents('php://input'), true);
        // Connect to the local database (replace with your credentials)
        $con = new mysqli("localhost","user","pass","kittytopia");
        if(mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }
        $this->input = $input; // Incoming request
        $this->con = $con; // Database connection
    }

    // Update one of the kitty's stats
    function updateKittyStats() {
        $results = new stdClass();
        $results->error = false;
        $results->input = $this->input;

        // Prepare SQL statement
        $stmt = $this->con->prepare("UPDATE cats SET cat_{$this->input['field']} = ? WHERE cat_first = ? LIMIT 1");

        // Bind parameters
        $stmt->bind_param("ss", $this->input['value'], $this->input['first']);

        // Execute statement
        $stmt->execute();

        // Check for errors
        if ($stmt->error) {
            $results->error = "Error: " . $stmt->error;
        } else {
            $results->success = "Record updated successfully";
        }

        // Close statement
        $stmt->close();
        echo json_encode($results);
    }

    // Update more than one of the kitty's stats at the same time
    function updateStats() {
        $results = new stdClass();
        $results->error = false;
        $results->input = $this->input;

        // Prepare SQL statement
        $n = 1;
        $query = "UPDATE cats SET ";
        $fieldPlaceholders = [];
        $values = [];
        $types = '';
        foreach ($this->input['values'] as $field => $value) {
            $fieldPlaceholders[] = "cat_$field = ?";
            $values[] = $value;
            $types .= 's';
        }

        $query .= implode(', ', $fieldPlaceholders);
        $query .= " WHERE cat_first = ? LIMIT 1";

        // Add condition value at the end
        $values[] = $this->input['first'];
        $types .= 's';

        // Prepare and bind statement
        if ($stmt = $this->con->prepare($query)) {
            $stmt->bind_param($types, ...$values);

            // Execute statement
            if ($stmt->execute()) {
                $results->success = "Update successful.";
            } else {
                $results->error = "Error executing query: " . $stmt->error;
            }
            // Close statement
            $stmt->close();
        } else {
            $this->error = "Error preparing statement: " . $this->con->error;
        }
        echo json_encode($results);
    }

    function getKittyStats() {
        $results = new stdClass();
        $results->error = false;
        $results->input = $this->input;

        // Build all kitties list
        $results->list = [];
        $q = "SELECT cat_id id, cat_first first, cat_last last FROM `cats` WHERE cat_retired = 0 ORDER BY cat_first ASC";
        $results->cat_query = $q;
        if(!$load = mysqli_query($this->con, $q)) {$results->error = "KITTIES LIST QUERY".mysqli_error($this->con); echo json_encode($results); exit;} // ERROR
        if(mysqli_num_rows($load) > 0) {
            while($run = mysqli_fetch_assoc($load)) {
                $results->list[] = $run;
            }
        }

        // Build profile info
        $stats = ['first','full','nick','last','gender','birth','occupation','gallery','height','weight','hairLength','hairColor','eyes','favfood','id','modified'];
        $q = "SELECT CONCAT(cat_first, ' ', cat_last) cat_full, cat_id, cat_first, cat_full, cat_nick, cat_last, cat_gender, cat_birth, cat_occupation, cat_hairLength, cat_hairColor, 
        cat_eyes, cat_height, cat_weight, cat_gallery, cat_personality, cat_hobbies, cat_favfood, cat_sayings, cat_history, cat_childhood, cat_adulthood, cat_timeline, 
        cat_modified FROM `cats` WHERE `cat_first`='".$this->input['first']."' LIMIT 1";
        $results->profile_query = $q;
        if(!$load = mysqli_query($this->con, $q)) {$results->error = "PROFILE INFO QUERY:\n".mysqli_error($this->con); echo json_encode($results); exit;} // ERROR
        $run = mysqli_fetch_array($load);
        $cat = [];
        foreach($run as $r => $s) {
            if(!is_numeric($r)) {
                $cat[$r] = $s;
            }
        }
        $id = $cat['cat_id'];
        // Sort data between stats panel and information panel
        foreach($cat as $key => $val) {
            if(in_array(substr($key,5), $stats)) {
                $results->stats[substr($key,5)] = $val;
            } else {
                $results->info[substr($key,5)] = $val;
            }
        }

        // Build the screenshots list
        $screenshots_query = "SELECT scr_title title, (SELECT COUNT(*) FROM subjects WHERE sub_cat = $id) AS total FROM `subjects` JOIN `screenshots` ON sub_image = scr_id WHERE sub_cat = $id ORDER BY RAND()";
        if(!$load_screenshots = mysqli_query($this->con, $screenshots_query)) {$results->error = "SCREENSHOTS LIST QUERY:\n".mysqli_error($this->con); echo json_encode($results); exit;} // ERROR
        if(mysqli_num_rows($load_screenshots) > 0) {
            $first = true;
            while($screenshots = mysqli_fetch_array($load_screenshots)) {
                if($first) {
                    $results->screenshots_total = $screenshots['total'];
                    $first = false;
                }
                $results->screenshots[] = $screenshots['title'];
            }
        } else {
            $results->screenshots = array();
            $results->screenshots_total = 0;
        }

        // Build friends lists
        $results->info['friends'] = $this->grab_relationships($id, 'friends');
        $results->info['relations'] = $this->grab_relations($id, $results->stats['first']." ".$results->stats['last']);
        echo json_encode($results);
    }

    function grab_relationships($id, $relationship_type) {
        $res = new stdClass();

        $relationship_label = [
            'friends' => 'Furrends'
        ];

        // Define relationship conditions and order based on the type
        switch ($relationship_type) {
            case 'friends':
                $condition = "rel_friend != '0'";
                $order_by = "rel_friend DESC, cat_LENGTH(one.mem_info) DESC, full ASC";
                break;
            default:
                return null;
        }

        // Define the query based on the type
        $query = "SELECT two.mem_id id, two.mem_info info, CONCAT(b.cat_first,' ',b.cat_last) full, 
                        rel_friend, DATE_FORMAT(rel_friendship, '%b %e, %Y') rel_friendship, DATE_FORMAT(rel_met, '%b %e, %Y') rel_met, 
                        cat_gender, cat_first, cat_id, rel_id
                FROM relationships rel
                JOIN members one ON one.mem_relID = rel.rel_id AND one.mem_catID = $id AND one.mem_role = 'P'
                JOIN members two ON two.mem_relID = rel.rel_id AND two.mem_catID != $id AND two.mem_role = 'P' AND two.mem_catID != '0'
                JOIN cats b ON b.cat_id = two.mem_catID
                WHERE $condition
                ORDER BY $order_by";

        $res->q = $query;
        if(!$load = mysqli_query($this->con, $query)) {$res->error = $relationship_type." QUERY\n".mysqli_error($this->con); echo json_encode($res); exit;} // ERROR
        $count = mysqli_num_rows($load);
        $str = "<h3>".$relationship_label[$relationship_type]." (".$count.")</h3>";

        if ($count > 0) {

            $last_type = "";
            // Loop through all search results
            while ($row = mysqli_fetch_array($load)) {
                $label = "<h4>";

                if($relationship_type === 'friends') {
                    if ($row['rel_friend'] != $last_type) {
                        $friend_array = array(1 => 'Casual', 2 => 'Close', 3 => 'Best');
                        $label .= $friend_array[$row['rel_friend']];
                        $last_type = $row['rel_friend'];
                    }
                }

                $label .= "</h4>";

                // Add field label if available
                if($label != "<h4></h4>") $str .= $label;
                $proper = $row['id'];
                $nam = strtolower(str_replace(" ", "-", $row['full']));
                $link = "/?name=" . $nam;

                // Add tiny avatar
                $str .= "<p><b><img class='tiny_av' src='src/assets/".$nam."_0.jpg' /> <a class='name' href='" . $link . "'>" . $row['full'] . "</a></b> ";

                // Add additional fields data if available
                if (!empty($row['rel_met'])) $str .= " (Met " . $row['rel_met'] . ")";
                if (!empty($row['rel_friendship'])) $str .= " (Friends " . $row['rel_friendship'] . ")";
                $str .= "</p><p>".($row['info'] !== '' ? $row['info'] : "<span style='color:#777'>TBA</span>")."</p>";
            }
        } else {
            $str .= "<span style='color:#777'>TBA</span>";
        }
        return $str;
    }

    function grab_relations($id, $name) {
        $res = new stdClass();
        $all_relationships = array();
        $query = "SELECT rel.rel_id id, rel.rel_name name, cats.cat_first first, cats.cat_last last, a.mem_role one, b.mem_role two FROM relationships rel JOIN members a ON rel.rel_id = a.mem_relID JOIN members b ON rel.rel_id = b.mem_relID JOIN cats ON cats.cat_id = b.mem_catID WHERE a.mem_catID = $id ORDER BY rel.rel_name ASC, b.mem_id ASC";
        $res->query = $query;
        if(!$load = mysqli_query($this->con, $query)) {$res->error = "GRAB RELATIONS\n".mysqli_error($this->con); echo json_encode($res); exit;} // ERROR
        $num_rows = mysqli_num_rows($load);
        $str = "";
        if($num_rows > 0) {
            $curr_id = 0;
            $parents = "";
            while($row = mysqli_fetch_array($load)) {
                $cat_name = strtolower($row['first']."-".$row['last']);
                $img_tag = "<a href='/?name=" . $cat_name . "'>";
                $img_tag .= "<img height='75px' src='src/assets/" . $cat_name . "_0.jpg' style='border-radius:100%;padding:0px 10px 0px 10px;' />";
                $img_tag .= "</a>";
                if($row['id'] == $curr_id) { // Still building the same relationship
                    if($row['two'] == "P") {
                        $parents .= " + " . $img_tag;
                    }
                } else { // New relationship
                    if($curr_id != 0) { // Add previous relationships to list
                        $all_relationships[] = $link . $edit . $parents;
                    }
                    $link = "<p><a href='/relationship/".$row['id']."'>".$row['name']."</a>";
                    $edit = "<a href='/edit-relationships/".$row['id']."'>";
                    $edit .= "<span class='material-symbols-outlined align-bottom'> edit </span>";
                    $edit .= "</a></p>";
                    $parents = $row['two'] == "P" ? "<p>" . $img_tag : "<p>";
                    $curr_id = $row['id'];
                }
            }
            // Add the final relationship
            $all_relationships[] = $link . $edit . $parents;
        }
        if(!empty($all_relationships)) {
            $count = count($all_relationships);
            $str .= "<div class='row'><div class='col'><h3 class='pb-4'>Relationships (".$count.")</h3>";
            foreach($all_relationships as $value) {$str .= $value;}
            $str .= "</div><div class='col'>";
            $str .= "<h3 class='pb-4'>Relationship Links</h3>";
            $str .= "<p><a href='/add-relationship'>Add New Relationship</a></p>";
            $str .= "<p><a href='/degrees'>Six Degrees of ".$name."</a></p>";
            $str .= "<p><a href='/relations'>Mutual Relationships</a></p>";
            $str .= "<p><a href='/connections'>Links from ".$this->input['first']." to ???</a></p>";
            $str .= "</div></div>";
        }
        $res->str = $str == "" ? "<span style='color:#777'>TBA</span>" : $str;
        return $res->str;
    }
}

$xp = new vueAPI;
// If request is in POST form, reject it
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}
// Fetch incoming data
$input = json_decode(file_get_contents('php://input'), true);
// If data is formatted correctly, process request
if(isset($input['func']) && !empty($input['func']) && method_exists($xp, $input['func'])) {
    $func = $input['func'];
    $xp->$func();
}
?>