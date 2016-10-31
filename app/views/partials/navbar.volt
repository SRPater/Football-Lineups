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
                <li><a href="/../cmtprg01-5/users/profile">Profile</a></li>
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
                {% if (session.get("admin") == 1) %}
                    <li><a href="/../cmtprg01-5/users">Admin</a></li>
                {% endif %}
            </ul>
            <p class="nav navbar-nav navbar-right">
                <a href="/../cmtprg01-5/logout" class="btn btn-primary">Logout</a>
            </p>
        </div>
    </div>
</nav>
