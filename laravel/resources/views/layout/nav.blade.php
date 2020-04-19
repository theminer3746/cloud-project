<nav class="navbar" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <a href="/" class="navbar-item">
            <span>Cloud Project</span>
        </a>
    </div>

    <a role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false" data-target="mainNav">
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
    </a>

    <div id="mainNav" class="navbar-menu">
        <div class="navbar-end">
            <a href="/" class="navbar-item">
                Home
            </a>

            @if(App\Auth::isLoggedIn())
                <a class="navbar-item" id="logoutButton">
                    Logout
                </a>
            @endif
        </div>
    </div>
</nav>
