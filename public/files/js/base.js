
function loadON()
{
    $("#load").fadeIn("fast")
    if ("activeElement" in document)
        document.activeElement.blur()
}
function loadOFF()
{
    $("#load").hide()
}

// Active tooltip bootstrap 5
    function activeTooltip()
    {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    }
    activeTooltip();