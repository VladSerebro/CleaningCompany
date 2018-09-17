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
                    }).then(res =>{
                        console.log(res);
                        window.location.reload();
                    }).catch((err) => {
                        console.error(err);
                    })
                }
            }
        });
    }

    // delete city
    const cities = document.getElementById('cities');

    if(cities)
    {
        cities.addEventListener('click', e => {
            if(e.target.className === 'btn btn-outline-danger btn-sm')
            {
                if(confirm('Are you sure?'))
                {
                    const id = e.target.getAttribute('data-id');

                    fetch(`/admin/city/delete/${id}`, {
                        method: 'DELETE'
                    }).then((res) => {
                        console.log(res);
                        window.location.reload()
                    }).catch((err) => {
                        console.error(err);
                    })
                }
            }
        });
    }

    // delete cleaner
    const cleaners = document.getElementById('cleaners');

    if(cleaners)
    {
        cleaners.addEventListener('click', e => {
            if(e.target.className === 'btn btn-outline-danger btn-sm')
            {
                const id = e.target.getAttribute('data-id');

                fetch(`/admin/cleaner/delete/${id}`, {
                    method: 'DELETE'
                }).then((res) => {
                    console.log(res);
                    window.location.reload()
                }).catch((err) => {
                    console.error(err);
                })
            }
        });
    }
});




