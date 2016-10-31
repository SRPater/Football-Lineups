<div class="page-header">
    <h1>Football Lineups</h1>
</div>

<img id="pitch" src="/../cmtprg01-5/img/pitch.png" />

<div class="row">
    <div class="col-sm-6">
        <h2>Welcome to Football Lineups!</h2>

        <p>Sign up below to start submitting your ultimate lineups or adding players to our database!</p>

        {{ link_to("users/new", "Sign Up", "class": "btn btn-primary") }}
    </div>
    <div class="col-sm-6">
        <h2>Already have an account? Login here</h2>

        {{ content() }}

        <div class="row">
            {{ form("login", "autocomplete": "off", "class": "form-horizontal") }}
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">Username:</label>
                <div class="col-sm-10">
                    {{ text_field("username", "size": 30, "class": "form-control", "id": "loginUsername") }}
                </div>
            </div>
            <div class="form-group">
                <label for="password" class="col-sm-2 control-label">Password:</label>
                <div class="col-sm-10">
                    {{ password_field("password", "size": 30, "class": "form-control", "id": "loginPassword") }}
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    {{ submit_button("Login", "class": "btn btn-primary") }}
                </div>
            </div>
            {{ end_form() }}
        </div>
    </div>
</div>
