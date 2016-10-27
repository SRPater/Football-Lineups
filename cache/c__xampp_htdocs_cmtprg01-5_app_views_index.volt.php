<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>Phalcon PHP Framework</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="/../cmtprg01-5/css/stylesheet.css">
    </head>
    <body>
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="navbar-collapse" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/../cmtprg01-5">FOOTBALL LINEUPS</a>
                </div>

                <div class="collapse navbar-collapse" id="navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                Users <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="/../cmtprg01-5/users">View</a></li>
                                <li><a href="/../cmtprg01-5/users/new">New</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                Players <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="/../cmtprg01-5/players">View</a></li>
                                <li><a href="/../cmtprg01-5/players/new">New</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                Lineups <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="/../cmtprg01-5/lineups">View</a></li>
                                <li><a href="/../cmtprg01-5/lineups/new">New</a></li>
                            </ul>
                        </li>
                    </ul>

                    <?php if ($this->session->has("id")): ?>
                        <p class="navbar-text navbar-right">Welcome, <?php echo $this->session->get("name"); ?>! <a href="logout">Logout</a></p>
                    <?php else: ?>
                        <?php echo $this->tag->form([
                            "login",
                            "class" => "navbar-form navbar-right"
                        ]); ?>

                        <?php if ($this->session->has("error")): ?>
                            <div class="form-group">
                                <p class="text-primary"><?php echo $this->session->get("error") ?></p>
                                <?php $this->session->remove("error") ?>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="fieldUsernameLogin" class="sr-only">Username</label>
                            <?php echo $this->tag->textField(["usernameLogin", "size" => 10, "class" => "form-control", "placeholder" => "Username", "id" => "fieldUsernameLogin"]); ?>
                        </div>

                        <div class="form-group">
                            <label for="fieldPasswordLogin" class="sr-only">Password</label>
                            <?php echo $this->tag->passwordField(["passwordLogin", "size" => 10, "class" => "form-control", "placeholder" => "Password", "id" => "fieldPasswordLogin"]); ?>
                        </div>

                        <div class="form-group">
                            <?php echo $this->tag->submitButton(["Login", "class" => "btn btn-primary"]); ?>
                        </div>

                        <?php echo $this->tag->endForm(); ?>

                    <?php endif; ?>
                </div>
            </div>
        </nav>

        <div class="container">
            <?= $this->getContent() ?>
        </div>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    </body>
</html>
