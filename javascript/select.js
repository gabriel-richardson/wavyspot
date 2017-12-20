function onChange(select) {
	var selectedOption = select.options[select.selectedIndex].text;
	str = window.location.search;
	str = replaceQueryParam("sort", selectedOption, str);
	window.location = window.location.pathname + str;
}

function replaceQueryParam(param, newval, search) {
    var regex = new RegExp("([?;&])" + param + "[^&;]*[;&]?");
    var query = search.replace(regex, "$1").replace(/&$/, '');

    return (query.length > 2 ? query + "&" : "?") + (newval ? param + "=" + newval : '');
}