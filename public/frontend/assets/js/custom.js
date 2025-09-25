jQuery(document).ready(function() {
    // jQuery('.pricing_moving').on('click', function (e) {
    //   e.preventDefault()
    //   jQuery('#myTab a[href="#moving"]').tab('show');
    // });
    // jQuery('.pricing_mobility').on('click', function (e) {
    //   e.preventDefault()
    //   jQuery('#myTab a[href="#mobility"]').tab('show')
    // });
    // jQuery('.pricing_contractor').on('click', function (e) {
    //   e.preventDefault()
    //   jQuery('#myTab a[href="#contractor"]').tab('show')
    // });
    
    
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
        var popover = new bootstrap.Popover(document.querySelector('.example-popover'), {
            container: 'body'
        });
        
    });
});
