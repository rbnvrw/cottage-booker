$(document).ready(function(){

	// Delete button functionality
    $(".cottage_booker__dashboard__button--delete-booking").click(function(e){
		currentAnchor = $(this);
		e.preventDefault();
        $('.cottage_booker__dashboard__confirm__delete--booking').dialog({
            buttons: [
                {
                    class: 'btn cancel',
                    click: function() {
                        $(this).dialog('close');
                    },
                    text: 'Terug'
                },
                {
                    class: 'btn btn-danger',
                    click: function() {
                        $(this).dialog('close');
			window.location.href = currentAnchor.attr('href');
                    },
                    html: '<i class="icon-remove-circle icon-white"></i> Annuleren'
                }
            ],
            dialogClass: 'ccm-ui',
            title: 'Reservering annuleren',
            width: 550,
            modal: true,
            height: 380
        });
    });
	
	$(".cottage_booker__dashboard__button--delete-cancellation").click(function(e){
		e.preventDefault();
		currentAnchor = $(this);
        $('.cottage_booker__dashboard__confirm__delete--cancellation').dialog({
            buttons: [
                {
                    class: 'btn cancel',
                    click: function() {
                        $(this).dialog('close');
                    },
                    text: 'Terug'
                },
                {
                    class: 'btn btn-danger',
                    click: function() {
                        $(this).dialog('close');
			window.location.href = currentAnchor.attr('href');
                    },
                    html: '<i class="icon-trash icon-white"></i> Verwijderen'
                }
            ],
            dialogClass: 'ccm-ui',
            title: 'Annulering verwijderen',
            width: 550,
            modal: true,
            height: 380
        });
    });
	
	$(".cottage_booker__dashboard__button--delete-exception").click(function(e){
		e.preventDefault();
		currentAnchor = $(this);
        $('.cottage_booker__dashboard__confirm__delete--exception').dialog({
            buttons: [
                {
                    class: 'btn cancel',
                    click: function() {
                        $(this).dialog('close');
                    },
                    text: 'Terug'
                },
                {
                    class: 'btn btn-danger',
                    click: function() {
                        $(this).dialog('close');
                        window.location.href = currentAnchor.attr('href');
                    },
                    html: '<i class="icon-trash icon-white"></i> Verwijderen'
                }
            ],
            dialogClass: 'ccm-ui',
            title: 'Uitzondering verwijderen',
            width: 550,
            modal: true,
            height: 380
        });
    });
    
    // Javascript to enable link to tab
    var url = document.location.toString();
    if (url.match('#')) {
        $('.nav-tabs a[href=#'+url.split('#')[1]+']').tab('show') ;
    } 

    // Change hash for page-reload
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        if(history.pushState) {
            history.pushState(null, null, e.target.hash);
        }
        else {
            location.hash = e.target.hash;
        }    
    });
    
});
