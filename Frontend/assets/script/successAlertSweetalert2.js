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

document.getElementById("success").addEventListener("click", (e) => {
    Swal2.fire({
        icon: "success",
        title: "Success",
    })
})

document.getElementById('toast-success').addEventListener('click', () => {
    Toast.fire({
        icon: 'success',
        title: 'Signed in successfully'
    })
})