import Swal from 'sweetalert2';

window.Swal = Swal;

// Toast untuk notifikasi singkat
window.Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
});

// Alert untuk pesan sukses
window.successAlert = (message) => {
    Toast.fire({
        icon: 'success',
        title: message
    });
};

// Alert untuk pesan error
window.errorAlert = (message) => {
    Toast.fire({
        icon: 'error',
        title: message
    });
};

// Alert untuk konfirmasi
window.confirmAlert = async (title, text) => {
    const result = await Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal'
    });
    return result.isConfirmed;
};

// Alert untuk pesan informasi
window.infoAlert = (message) => {
    Swal.fire({
        icon: 'info',
        title: message,
        showConfirmButton: true
    });
};