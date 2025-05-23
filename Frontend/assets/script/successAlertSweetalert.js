const Swal2 = Swal.mixin({
    customClass: {
        input: 'form-control'
    }
})

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
})


const urlParam = new URLSearchParams(window.location.search);
if (urlParam.get('action') === 'deleted') {
    Swal2.fire({
        icon: "success",
        title: "Eliminato con successo",
    })
}