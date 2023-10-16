jQuery(document).ready(function($) {
    var currentPage = 1;
    
    function loadProjects(page, keyword) {
        $.ajax({
            type: 'POST',
            url: cpl_ajax_params.cpl_ajax_url,
            data: {
                action: 'cpl_get_projects',
                page: page,
                keyword: keyword,
                security: cpl_ajax_params.cpl_nonce,
            },
            success: function(response) {
                console.log("success")
                $('#cpl-project-list').html(response);
            }
        });
    }

    loadProjects(currentPage, '');

    $('#cpl-search').on('input', function() {
        var keyword = $(this).val();
        loadProjects(1, keyword);
    });

    $('#cpl-pagination-prev').click(function(e) {
        e.preventDefault();
        if (currentPage > 1) {
            currentPage--;
            loadProjects(currentPage, $('#cpl-search').val());
        }
    });

    $('#cpl-pagination-next').click(function(e) {
        e.preventDefault();
        currentPage++;
        loadProjects(currentPage, $('#cpl-search').val());
    });
});
