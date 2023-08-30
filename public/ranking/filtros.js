function loadPage(){
    $('[data-toggle="tooltip"]').tooltip();
    const fechaInicio = document.querySelector("#txtFechaInicio");
    const fechaFin = document.querySelector("#txtFechaFin");
    for (const btnExportar of document.querySelectorAll('.btn-exportar')) {
        btnExportar.addEventListener("click",exportar);
    }
    function exportar(e) {
        e.preventDefault();
        const enlace = document.createElement("a");
        enlace.target = "_blank";
        const buscador = document.querySelector("[type='search'].form-control");
        enlace.href = window.origin + '/intranet/ranking/' + e.target.dataset.type + "/reportes/" + e.target.dataset.accion + "?fechaInicio=" + fechaInicio.value + "&fechaFin=" + fechaFin.value + "&buscador=" + buscador.value;
        document.body.appendChild(enlace);
        enlace.click();
        enlace.remove(); 
    }
}
window.addEventListener("DOMContentLoaded",loadPage);