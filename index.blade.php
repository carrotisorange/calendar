<x-new-layout-calendar>
    @section('styles')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <style>
        body {
            font-family: "Poppins";
        }

        .fc-state-default {
            background-color: #E5E1E8;
        }

        .fc-event {
            position: relative;
            /* for resize handle and other inner positioning */
            display: block;
            /* make the <a> tag block */
            font-size: .85em;
            line-height: 2;
            border-radius: 6px;
            border: 1px solid #4D346F;
            /* default BORDER color */
            font-weight: normal;
            /* undo jqui's ui-widget-header bold */
        }

        .fc-event,
        .fc-event-dot {
            background-color: #4D346F;
            /* default BACKGROUND color */
        }
    </style>

    @endsection

    @section('title','Calendar | '. env('APP_NAME'))

    <div class="mt-8">
        <div class="max-full mx-auto sm:px-6">
            @livewire('calendar-index-component', ['property' => $property, 'events' => $events, 'unitUuid' => null])
        </div>
    </div>

    @section('scripts')
    <script type="text/javascript">
        $(document).ready(function(){

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var booking = @json($events);

            $('#calendar').fullCalendar({
                header:{
                    left:'prev, next today',
                    center:'title',
                    right: 'month,listWeek,agendaWeek'
                },
                defaultView: 'month',
                events: booking,
                selectable: true,
                selectHelper: true,
                select: function(start, end, allDays){
                    $('#bookingModal').modal('toggle');

                    $('#saveBtn').click(function(){
                        document.getElementById("saveBtn").innerHTML = "Loading...";

                        var guest = $('#guest').val();
                        var email = $('#email').val();
                        var mobile_number = $('#mobile_number').val();
                        var property_uuid = $('#property_uuid').val();
                        var unit_uuid = $('#unit_uuid').val();
                        var movein_at = moment(start).format('YYYY-MM-DD');
                        var moveout_at = moment(end).format('YYYY-MM-DD');
                        var agent_id = $('#agent_id').val();
                        var no_of_guests = $('#no_of_guests').val();
                        var no_of_children = $('#no_of_children').val();
                        var no_of_senior_citizens = $('#no_of_senior_citizens').val();
                        var no_of_pwd = $('#no_of_pwd').val();
                        var remarks = $('#remarks').val();

                        $.ajax({
                            url:"{{ route('calendar.store') }}",
                            type:"POST",
                            dataType: 'json',
                            data:{
                                guest, email, mobile_number, movein_at, moveout_at, unit_uuid, property_uuid,agent_id,
                                no_of_guests, no_of_children, no_of_senior_citizens, no_of_pwd,remarks
                            },
                            success:function(response){
                                $('#bookingModal').modal('hide')
                                $('#calendar').fullCalendar('renderEvent',{
                                    'id': response.uuid,
                                    'title':response.guest,
                                    'start':response.movein_at,
                                    'end':response.moveout_at,
                                });

                                swal("Success!", "Guest Created!", "success");

                                document.getElementById("saveBtn").innerHTML = "Book";
                                document.getElementById("guest").value = "";
                                document.getElementById("email").value = "";
                                document.getElementById("mobile_number").value = "";
                                document.getElementById("unit_uuid").value = "";
                                document.getElementById("agent_id").value = "";
                                document.getElementById("no_of_guests").value = "";
                                document.getElementById("no_of_children").value = "";
                                document.getElementById("no_of_senior_citizens").value = "";
                                document.getElementById("no_of_pwd").value = "";
                                document.getElementById("remarks").value = "";

                            },
                            error:function(error){
                                if(error.responseJSON.errors) {
                                    document.getElementById("saveBtn").innerHTML = "Book";

                                    $('#guestError').html(error.responseJSON.errors.guest);
                                    $('#emailError').html(error.responseJSON.errors.email);
                                    $('#mobileNumberError').html(error.responseJSON.errors.mobile_number);
                                    $('#unitUuidError').html(error.responseJSON.errors.unit_uuid);
                                    $('#agentIdError').html(error.responseJSON.errors.agent_id);
                                    $('#noOfGuestsError').html(error.responseJSON.errors.no_of_guests);
                                    $('#noOfChildrenError').html(error.responseJSON.errors.no_of_children);
                                    $('#noOfCitizenError').html(error.responseJSON.errors.no_of_senior_citizens);
                                    $('#noOfPwdError').html(error.responseJSON.errors.no_of_pwd);
                                    $('#remarkError').html(error.responseJSON.errors.remarks);
                                }
                            }
                        });
                    });


                },
                editable: true,
                eventDrop: function(event){
                    var id = event.id;
                    var movein_at = moment(event.start).format('YYYY-MM-DD');
                    var moveout_at = moment(event.end).format('YYYY-MM-DD');

                    $.ajax({
                            url:"{{ route('calendar.update', '') }}" + '/' + id,
                            type:"PATCH",
                            dataType: 'json',
                            data:{
                                movein_at, moveout_at
                            },
                            success:function(response){
                                swal("Success!", "Guest Updated!", "success");
                            },
                            error:function(error){
                                console.log(error)
                            }
                        });
                },
                eventClick: function(event){
                    window.location.href = "guest/"+event.id;
                }
            });
            $("#bookingModal").on("hidden.bs.modal", function(){
            $("#saveBtn").unbind();
            });
        });
    </script>
    @endsection

</x-new-layout-calendar>
