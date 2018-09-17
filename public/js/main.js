$(document).ready(function(){

    const bookings = document.getElementById('bookings');
    const cities = document.getElementById('cities');
    const cleaners = document.getElementById('cleaners');
    const customers = document.getElementById('customers');

    if(bookings)
        deleteEntity(bookings, '/admin/booking/delete');
    if(cities)
        deleteEntity(cities, '/admin/city/delete');
    if(cleaners)
        deleteEntity(cleaners, '/admin/cleaner/delete');
    if(customers)
        deleteEntity(customers, '/admin/customer/delete');

});

function deleteEntity($table, $path)
{
    $table.addEventListener('click', e => {
        if (e.target.className === 'btn btn-outline-danger btn-sm') {
            const id = e.target.getAttribute('data-id');

            fetch($path + `/${id}`, {
                method: 'DELETE'
            }).then(
                (res) => {
                    console.log(res);
                    window.location.reload();
                },
                (error) => {
                    alert(error);
                }
            ).catch((err) => {
                console.error(err);
            })
        }
    });
}



