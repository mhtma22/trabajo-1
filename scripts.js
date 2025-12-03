let productos = [];
let productosPorPagina = 6;
let paginaActual = 1;

// Cargar productos
document.getElementById("menu-item-products")?.addEventListener("click", e=>{
    e.preventDefault();
    fetch('product.html')
    .then(res=>res.text())
    .then(html=>{
        document.getElementById('content-products').innerHTML=html;
        agregarEventosFiltro();
        cargarProductos();
    });
});

function agregarEventosFiltro(){
    document.getElementById("prevBtn").addEventListener("click", ()=>{if(paginaActual>1){paginaActual--; renderizarProductos();}});
    document.getElementById("nextBtn").addEventListener("click", ()=>{if(paginaActual*productosPorPagina<productos.length){paginaActual++; renderizarProductos();}});
    document.getElementById("btnBuscar").addEventListener("click", e=>{
        e.preventDefault();
        const cat=document.getElementById("categoriaSelect").value;
        const busq=document.getElementById("in_search").value;
        paginaActual=1;
        cargarProductos(cat,busq);
    });
}

function cargarProductos(categoria=null,busqueda=null){
    let url="http://localhost/serviceweb/product.php";
    if(categoria && categoria!="5") url+="?cat="+categoria;

    fetch(url).then(res=>res.json()).then(data=>{
        productos=data;
        if(busqueda) productos=productos.filter(p=>p.nombre.toLowerCase().includes(busqueda.toLowerCase()));
        renderizarProductos();
    });
}

function renderizarProductos(){
    const cont=document.getElementById("main-products");
    if(!cont) return;
    cont.innerHTML="";
    const inicio=(paginaActual-1)*productosPorPagina;
    const fin=inicio+productosPorPagina;
    const pagProductos=productos.slice(inicio,fin);

    pagProductos.forEach(item=>{
        cont.innerHTML+=`
        <div class="col mb-4">
            <div class="card h-100 shadow-sm">
                <img src="${item.ruta}" class="card-img-top" style="height:180px;object-fit:cover;">
                <div class="card-body text-center">
                    <h5 class="fw-bold mb-2" style="font-size:1rem;">${item.nombre}</h5>
                    <h5 class="fw-bold mb-2" style="font-size:0.8rem;">${item.categoria}</h5>
                    <p class="text-success fw-bold mb-0" style="font-size:0.9rem;">S/. ${item.precio}</p>
                </div>
                <div class="card-footer text-center">
                    <button class="btn btn-outline-dark btn-sm btn-vermas" data-id="${item.id}" data-bs-toggle="modal" data-bs-target="#vistaRapidaModal">Ver más</button>
                </div>
            </div>
        </div>`;
    });

    document.getElementById("prevBtn").disabled=paginaActual===1;
    document.getElementById("nextBtn").disabled=paginaActual*productosPorPagina>=productos.length;

    const pageNumbers=document.getElementById("pageNumbers");
    pageNumbers.innerHTML="";
    for(let i=1;i<=Math.ceil(productos.length/productosPorPagina);i++){
        const btn=document.createElement("button");
        btn.className=`btn btn-sm mx-1 ${i===paginaActual?"btn-dark":"btn-outline-dark"}`;
        btn.textContent=i;
        btn.addEventListener("click", ()=>{paginaActual=i; renderizarProductos();});
        pageNumbers.appendChild(btn);
    }
}

// MODAL DETALLE Y AGREGAR CARRITO
document.addEventListener("click", async e=>{
    if(e.target.classList.contains("btn-vermas")){
        const id=e.target.dataset.id;
        const res=await fetch(`http://localhost/serviceweb/productdetail.php?id=${id}`);
        const data=await res.json();
        document.getElementById("modal-img").src=data.ruta;
        document.getElementById("modal-nombre").innerText=data.nombre;
        document.getElementById("modal-cat").innerText=data.categoria;
        document.getElementById("modal-precio").innerText="S/. "+data.precio;
        document.getElementById("btnAgregarCarrito").dataset.id=id;
    }

    if(e.target.id==="btnAgregarCarrito"){
        const producto_id=e.target.dataset.id;
        const talla=document.getElementById("modal-talla").value;
        const color=document.getElementById("modal-color").value;
        const usuario_id=sessionStorage.getItem("usuario_id")||1;

        let form=new FormData();
        form.append("usuario_id",usuario_id);
        form.append("producto_id",producto_id);
        form.append("talla",talla);
        form.append("color",color);

        const res=await fetch("http://localhost/serviceweb/carrito.php?accion=agregar",{method:"POST",body:form});
        const data=await res.json();

        const alertDiv=document.createElement("div");
        alertDiv.className="alert alert-success mt-2";
        alertDiv.innerText=data.mensaje;
        document.getElementById("vistaRapidaModal").querySelector(".modal-body").appendChild(alertDiv);
        setTimeout(()=>alertDiv.remove(),3000);
    }
});

// LOGIN - Modal
// Detectar sesión al cargar la página
document.addEventListener("DOMContentLoaded", () => {
    const usuario = sessionStorage.getItem("nombre");
    if(usuario) {
        mostrarLogout(usuario);
    }
});

// Función para mostrar logout y ocultar login
function mostrarLogout(nombre) {
    document.getElementById("btnLogin").classList.add("d-none");
    const logoutBtn = document.getElementById("btnLogout");
    logoutBtn.classList.remove("d-none");
    logoutBtn.innerHTML = `<i class="bi-box-arrow-right me-1"></i> Cerrar Sesión (${nombre})`;
}

// LOGIN
document.addEventListener("click", async (event) => {
    if(event.target.id === "btnLoginModal") {
        const user = document.getElementById("inpUsuario").value.trim();
        const pass = document.getElementById("inpClave").value.trim();
        const mensaje = document.getElementById("loginMensaje");

        if(!user || !pass) {
            mensaje.innerText = "Por favor ingresa usuario y contraseña";
            mensaje.classList.add("text-danger");
            return;
        }

        let datos = new FormData();
        datos.append("usuario", user);
        datos.append("clave", pass);

        try {
            const r = await fetch("http://localhost/serviceweb/login.php", {
                method: "POST",
                body: datos
            });

            const res = await r.json();

            if(res.success) {
                // Guardar sesión
                sessionStorage.setItem("usuario_id", res.usuario_id);
                sessionStorage.setItem("nombre", res.nombre);

                // Cerrar modal
                let modal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
                modal.hide();

                // Limpiar inputs y mensajes
                document.getElementById("inpUsuario").value = "";
                document.getElementById("inpClave").value = "";
                mensaje.innerText = "";

                // Mostrar logout
                mostrarLogout(res.nombre);

            } else {
                mensaje.classList.add("text-danger");
                mensaje.innerText = "Usuario o clave incorrectos";
            }

        } catch(err) {
            mensaje.classList.add("text-danger");
            mensaje.innerText = "Error al conectar con el servidor";
            console.error(err);
        }
    }
});

// LOGOUT
document.addEventListener("click", async (event) => {
    if(event.target.id === "btnLogout") {
        await fetch("http://localhost/serviceweb/logout.php");
        sessionStorage.clear();
        location.reload();
    }
});
