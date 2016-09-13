<?php

    session_start();

    $error = "";

    if(array_key_exists("logout", $_GET)) {

      unset($_SESSION);
      setcookie("id","", time() - 60*60);
      $_COOKIE["id"] = "";
    
    } else if ((array_key_exists("id", $_SESSION) AND $_SESSION['id']) OR (array_key_exists("id", $_COOKIE) AND $_COOKIE['id'])) {

      header("Location: loggedinpage.php"); 

    }

    if (array_key_exists("submit", $_POST)) {
  
      include("connection.php");
      
    
      if (!$_POST['email']) {
 
        $error .= "An email address is required<br>";

      }
 
      if (!$_POST['password']) {
 
        $error .= "A password is required<br>";

      }
 
      if ($error != "") {

           $error = "<p>There were error(s) in your form:</p>".$error;

        } else {

          if ($_POST['signUp'] == '1') {

                  $query = "SELECT id FROM `users` WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."' LIMIT 1";

                    $result = mysqli_query($link, $query);

                  if (mysqli_num_rows($result) > 0) {

                    $error = "That email address is taken.";

                  } else {

                      $query = "INSERT INTO `users` (`email`, `password`) VALUES ('".mysqli_real_escape_string($link, $_POST['email'])."','".mysqli_real_escape_string($link, $_POST['password'])."')";

                      if (!mysqli_query($link, $query)) {

                        $error = "<p>Could not sign you up- please try again later.</p>";

                      } else {

                          $query = "UPDATE `users`  SET password = '".md5(md5(mysqli_insert_id($link)).$_POST['password'])."' WHERE id = ".mysqli_insert_id($link)." LIMIT 1"; 

                          mysqli_query($link, $query);

                          $_SESSION['id'] = mysqli_insert_id($link);

                          if ($_POST['stayLoggedIn'] == '1') {

                            setcookie("id", mysqli_insert_id($link), time() + 60*60*24*365);

                          }

                            header("Location: loggedinpage.php");


                      }

                   } 

            } else {
  
                $query = "SELECT * FROM `users` WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."'";

                $result = mysqli_query($link, $query);

                $row = mysqli_fetch_array($result);

                if (isset($row)) {

                  $hashedPassword = md5(md5($row['id']).$_POST['password']);
                
                    if ($hashedPassword == $row['password']) {

                      $_SESSION['id'] = $row['id'];

                      if ($_POST['stayLoggedIn'] == '1') {

                            setcookie("id", $row['id'], time() + 60*60*24*365);

                          }

                          header("Location: loggedinpage.php");

                      }  else {

                           $error = "That email/password combination could not be found.";
                      }

                 } else {

                     $error = "That email/password combination could not be found.";
                 }

            }
 
        }
  }



?>
<?php include("header.php"); ?>
      
    <div class="container" id="homePageContainer"> 
    
        <h1><strong id="brandTitle">Secret Note</strong></h1>
      <h2><strong class="lead" id="leadLine">Store your thoughts with Secret Note</strong></h2>
    
    <div id="error"><?php if ($error !="") {
    echo '<div class="alert alert-danger" role="alert">'.$error.'</div>';
    } ?></div>

<form method="post" id="signUpForm">
    <p class="lead">Interested Sign Up Now!</p>
   
        <fieldset class="form-group">     
          <input class="form-control" type="email" name="email" placeholder="Your Email" style="background:none;color:#FFF;">
        </fieldset>

        <fieldset class="form-group">  
          <input class="form-control" type="password" name="password" placeholder="Password" style="background:none;color:#FFF;">
        </fieldset>

        <div class="checkbox">
          <label>
            <input type="checkbox" name="stayLoggedIn" value=1> Stay Logged In
          </label>
        </div>

        <fieldset class="form-group"> 
          <input type="hidden" name="signUp" value="1">  
          <input class="btn btn-success" type="submit" name="submit" value="Sign Up!" style="opacity: 0.9;
         filter: alpha(opactity=90); ">
        </fieldset>
    
        <p><a class="toggleForms">Log In</a></p>

</form>

<form method="post" id="logInForm">
   <p class="lead">Log In using your username and password</p>
      <fieldset class="form-group">   
        <input class="form-control" type="email" name="email" placeholder="Your Email" style="background:none;color:#FFF;">
      </fieldset>


      <fieldset class="form-group">     
        <input class="form-control" type="password" name="password" placeholder="Password" style="background:none;color:#FFF;">
      </fieldset>

      <div class="checkbox">
        <label>
          <input type="checkbox" name="stayLoggedIn" value=1> Stay Logged In
        </label>
      </div>

      <fieldset class="form-group"> 
        <input type="hidden" name="signUp" value="0">  
          <input class="btn btn-info" type="submit" name="submit" value="Log In" style="opacity: 0.90;
         filter: alpha(opactity=90); "> 
      </fieldset>
      
      <p><a class="toggleForms">Sign Up</a></p>

</form>
      </div>

<?php include("footer.php"); ?>