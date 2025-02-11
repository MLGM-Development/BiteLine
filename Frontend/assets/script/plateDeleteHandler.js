function deletePlate(id) {
    if (confirm("Sei sicuro di voler eliminare questo piatto?")) {
        document.getElementById("delete_id").value = id;
        document.getElementById("deleteForm").submit();
    }
}