$(function () {
    $("#logoutButton").click(function (e) {
        e.preventDefault();
        $.ajax({
            url: "/auth/logout",
            data: {
                _token: xrf_token
            },
            dataType: "json",
            type: "POST",
            success: function () {
                window.location.replace("/auth/login");
            }
        });
    });
});
