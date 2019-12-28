function unset_all_cookies() {
    // for debug purposes only
    var cookie_names = Object.keys(cookie.all());
    cookie.remove(cookie_names);
    cookie.removeSpecific(cookie_names, { 'path': '/' });
}

console.log(cookie.all());
// unset_all_cookies();

function confirm_action() {
    if (!confirm('Are you sure?')) {
        return false;
    }
}

function show_error(msg) {
    $.fancybox.open('<div class="message"><h2>Error</h2><p>' + msg + '</p></div>');
}

function check_kpi() {
    var value = document.getElementById('name').value;

    if (!value) {
        show_error('Name cannot be empty.');
        return false;
    }
    return true;
}

function check_kpi_entry() {
    var value = document.getElementById('data').value;

    if (!value) {
        show_error('Value cannot be empty');
        return false;
    }
    return true;
}
