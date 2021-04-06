$(document).ready(function() {

    var oCalendar = $('.cottage_booker__view--full-calendar');

    oCalendar.fullCalendar({
        header: {
            left: 'title',
            center: 'today',
            right: 'month,basicWeek prev,next'
        },
        firstDay: 1,
        monthNames: ["januari", "februari", "maart", "april", "mei", "juni", "juli", "augustus", "september", "oktober", "november", "december"],
        monthNamesShort: ["jan", "feb", "maa", "apr", "mei", "jun", "jul", "aug", "sep", "okt", "nov", "dec"],
        dayNames: ['zondag', 'maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag'],
        dayNamesShort: ["zo", "ma", "di", "wo", "do", "vr", "za", "zo"],
        buttonText: {today: '<i class="icon-home"></i> vandaag', month: 'maand', week: 'week', day: 'dag'},
        eventSources: [
            {
                url: $('.cottage_booker__action--fetchall').attr('href'),
                type: 'POST',
                allDayDefault: true
            },
            {
                url: $('.cottage_booker__action--fetchallexceptions').attr('href'),
                type: 'POST',
                allDayDefault: true,
                color: 'green'
            }
        ],
        dayClick: function(dayDate, allDay, ev, view) {
            populateBookingForm('', dayDate, dayDate, '', 1);
            setFormType('new');
            showView('book-form');
        },
        eventClick: function(calEvent, jsEvent, view) {
            if (calEvent.type == 'exception'){

                $('.cottage_booker__exception').html('<p>'+calEvent.notes+'</p><p>'+calEvent.title+'</p>');

                $('.cottage_booker__exception').dialog({
                    buttons: [
                        {
                            class: 'btn cancel',
                            click: function() {
                                $(this).dialog('close');
                            },
                            text: 'Terug'
                        }
                    ],
                    dialogClass: 'ccm-ui',
                    title: 'Uitzondering',
                    width: 550,
                    modal: true,
                    height: 380
                });
            }else{
                if (calEvent.authorized) {
                    populateBookingForm(calEvent.id, calEvent.start, calEvent.end, calEvent.notes, calEvent.persons);
                    setFormType('update');
                    showView('book-form');
                } else {
                    flashMessage('U kunt deze reservering niet aanpassen!', 'error');
                }
            }
        }
    });

    // Add new button to full calendar
    addCalButton('.cottage_booker__view--full-calendar', 'center', 'cottage_booker__action--new');

    // Form button listeners
    $('.cottage_booker__book-form__button--book').click(function(event) {
        event.preventDefault();
        // New booking or update booking
        createOrUpdate();
        // Formulier legen
        populateBookingForm('', '', '', '', 1);
    });

    // Show the calendar
    $('.cottage_booker__book-form__button--view-calendar').click(function(e) {
        e.preventDefault();
        showView('full-calendar');
    });

    // Book new event
    $('.cottage_booker__action--new').click(function(event) {
        event.preventDefault();
        setFormType('new');
        showView('book-form');
    });

    // Initialize datepicker options
    $('.ccm-input-date').datepicker(
            $.extend(
                    {'dateFormat': 'dd-mm-yy'},
            $.datepicker.regional['nl'],
                    {minDate: getFormattedDate(new Date())},
            {onSelect: function(dateText) {
                    updateBookingCredits();
                }}
            )
            );

    // Delete button functionality
    $(".cottage_booker__book-form__button--delete").click(function(e){
        $('.cottage_booker__book-form__confirm__delete').dialog({
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
                        showView('full-calendar');
                        deleteBooking($('#cottage_booker__book-form__id').val());
                    },
                    html: '<i class="icon-trash icon-white"></i> Verwijderen'
                }
            ],
            dialogClass: 'ccm-ui',
            title: 'Reservering verwijderen',
            width: 550,
            modal: true,
            height: 380
        });
    });

});

// Call create or update functions
function createOrUpdate() {
    if (!$('#cottage_booker__book-form__id').val()) {
        // Nieuw
        var bError = false;
        if (!$('#cottage_booker__book-form__start').val()) {
            $('#cottage_booker__book-form__start').parents('.control-group').addClass('error');
            bError = true;
        }
        if (!$('#cottage_booker__book-form__end').val()) {
            $('#cottage_booker__book-form__end').parents('.control-group').addClass('error');
            bError = true;
        }
        if (!$('#cottage_booker__book-form__persons').val()) {
            $('#cottage_booker__book-form__persons').parents('.control-group').addClass('error');
            bError = true;
        }
        if(!bError) {
            var start = $.fullCalendar.parseDate($('#cottage_booker__book-form__start').datepicker("getDate"));
            var end = $.fullCalendar.parseDate($('#cottage_booker__book-form__end').datepicker("getDate"));
            newBooking(start, end, $('#cottage_booker__book-form__notes').val(), $('#cottage_booker__book-form__persons').val());
        }
    } else {
        // Update
        var bError = false;
        if (!$('#cottage_booker__book-form__start').val()) {
            $('#cottage_booker__book-form__start').parents('.control-group').addClass('error');
            bError = true;
        }
        if (!$('#cottage_booker__book-form__end').val()) {
            $('#cottage_booker__book-form__end').parents('.control-group').addClass('error');
            bError = true;
        }
        if (!$('#cottage_booker__book-form__persons').val()) {
            $('#cottage_booker__book-form__persons').parents('.control-group').addClass('error');
            bError = true;
        }
        if(!bError) {
            var start = $.fullCalendar.parseDate($('#cottage_booker__book-form__start').datepicker("getDate"));
            var end = $.fullCalendar.parseDate($('#cottage_booker__book-form__end').datepicker("getDate"));
            updateBooking($('#cottage_booker__book-form__id').val(), start, end, $('#cottage_booker__book-form__notes').val(), $('#cottage_booker__book-form__persons').val());
        }
    }
}

// Show a particular view
function showView(view) {
    $('.cottage_booker__view').fadeOut("slow", function() {
        $('.cottage_booker__view--'+view).fadeIn();
    });
}

// Set the booking form type to new or update
function setFormType(sType) {
    if (sType == 'new') {
        $('.cottage_booker__view--book-form__legend').text('Nieuwe reservering');
        $('.cottage_booker__book-form__button--book').html('<i class="icon-ok icon-white"></i> Reserveer');
        $('.cottage_booker__book-form__button--delete').hide();
    } else if (sType == 'update') {
        $('.cottage_booker__view--book-form__legend').text('Bewerk reservering');
        $('.cottage_booker__book-form__button--book').html('<i class="icon-ok icon-white"></i> Reservering bijwerken');
        $('.cottage_booker__book-form__button--delete').show();
    }
}

// Populate booking form values
function populateBookingForm(id, start, end, notes, persons) {
    $('#cottage_booker__book-form__id').val(id);
    $('#cottage_booker__book-form__start').datepicker("setDate", $.fullCalendar.formatDate(start, 'dd-MM-yyyy'));
    if (!end) {
        $('#cottage_booker__book-form__end').datepicker("setDate", $.fullCalendar.formatDate(start, 'dd-MM-yyyy'));
    } else {
        $('#cottage_booker__book-form__end').datepicker("setDate", $.fullCalendar.formatDate(end, 'dd-MM-yyyy'));
    }
    $('#cottage_booker__book-form__notes').val(notes);
    $('#cottage_booker__book-form__persons').val(persons);
    updateBookingCredits();
}

// Display an alert message
function flashMessage(text, type) {
    type = type || "success";
    $('.alert').alert('close');
    $('.cottage_booker__flashmessages').prepend("<div class='alert alert-" + type + " fade in'><a href='#' class='close' data-dismiss='alert'>&times;</a>" + text + "</div>").alert();
}

// Save the booking in the db
function updateBooking(id, start, end, notes, persons) {
    var url = $('.cottage_booker__action--update').attr('href');
    $.get(url, {
        id: id,
        start: $.fullCalendar.formatDate(start, 'yyyy-MM-dd'),
        end: $.fullCalendar.formatDate(end, 'yyyy-MM-dd'),
        notes: notes,
        persons: persons
    }
    ).done(function(data) {
        var data = JSON.parse(data);
        flashMessage(data.message, data.status);
        updateCredits(data.credits);
        if(data.status !== 'error'){
            showView('full-calendar');
            $('.cottage_booker__view--full-calendar').fullCalendar('refetchEvents');
        }
    });
}

// Insert the new booking in the db
function newBooking(start, end, notes, persons) {
    var url = $('.cottage_booker__action--new').attr('href');
    $.get(url, {
        start: $.fullCalendar.formatDate(start, 'yyyy-MM-dd'),
        end: $.fullCalendar.formatDate(end, 'yyyy-MM-dd'),
        notes: notes,
        persons: persons
    }
    ).done(function(data) {
        var data = JSON.parse(data);
        flashMessage(data.message, data.status);
        updateCredits(data.credits);
        if(data.status !== 'error'){
            showView('full-calendar');
            $('.cottage_booker__view--full-calendar').fullCalendar('refetchEvents');
        }
    });
}

// Delete the booking from the database
function deleteBooking(id) {
    var url = $('.cottage_booker__action--delete').attr('href');
    $.get(url, {
        id: id
    }
    ).done(function(data) {
        var data = JSON.parse(data);
        flashMessage(data.message, data.status);
        updateCredits(data.credits);
        $('.cottage_booker__view--full-calendar').fullCalendar('refetchEvents');
    });
}

// Add a button to the fullcalendar header
function addCalButton(parent, where, className) {
    $('.' + className).prependTo(parent + " td.fc-header-" + where);
    var text = $('.' + className).html();
    var my_button = '<span class="fc-button fc-state-default fc-corner-right fc-corner-left">' + text + '</span>'
            + '<span class="fc-header-space"></span>';
    $('.' + className).html(my_button);
    $('.' + className).show();
}

// Update the user credits
function updateCredits(credits) {
    $('.cottage_booker-i-user-credits').text(credits);
    if (credits == 1) {
        $('.cottage_booker-i-user-credits--suffix').text('schelp');
    } else {
        $('.cottage_booker-i-user-credits--suffix').text('schelpen');
    }
}

// Update the calculated credits of the booking form
function updateBookingCredits() {
    if (!$('#cottage_booker__book-form__start').val()) {
        $('#cottage_booker__book-form__start').parents('.control-group').addClass('warning');
    } else if (!$('#cottage_booker__book-form__end').val()) {
        $('#cottage_booker__book-form__end').parents('.control-group').addClass('warning');
    } else {
        $('#cottage_booker__book-form__end').parents('.control-group').removeClass('warning');
        $('#cottage_booker__book-form__start').parents('.control-group').removeClass('warning');
        var url = $('.cottage_booker__action--credits').attr('href');
        var start = $.fullCalendar.parseDate($('#cottage_booker__book-form__start').datepicker("getDate"));
        var end = $.fullCalendar.parseDate($('#cottage_booker__book-form__end').datepicker("getDate"));
        $.get(url, {
            start: $.fullCalendar.formatDate(start, 'yyyy-MM-dd'),
            end: $.fullCalendar.formatDate(end, 'yyyy-MM-dd')
        }
        ).done(function(data) {
            var data = JSON.parse(data);
            $('#cottage_booker__book-form__credits').val(data.credits);
        });
    }
}

function getFormattedDate(date) {
    var day = date.getDate();
    var month = date.getMonth() + 1;
    var year = date.getFullYear().toString();
    return day + '-' + month + '-' + year;
}
