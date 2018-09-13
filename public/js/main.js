$(document).ready(function(){

    // delete booking
    const bookings = document.getElementById('bookings');

    if(bookings)
    {
        bookings.addEventListener('click', e => {
            if(e.target.className === 'btn btn-outline-danger btn-sm')
            {
                if(confirm('Are you sure?'))
                {
                    const id = e.target.getAttribute('data-id');

                    fetch(`/admin/booking/delete/${id}`, {
                        method: 'DELETE'
                    }).then(res => window.location.reload())
                }
            }
        });
    }
});




