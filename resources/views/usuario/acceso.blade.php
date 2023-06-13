<!DOCTYPE html>
<html lang="es">
<head>
    @include('helper.meta')
    <link rel="stylesheet" href="/usuario/login.css">
    <script src="/usuario/login.js"></script>
    <title>Acceso</title>
</head>
<body>
  <div class="container login">
    <div class="row justify-content-center">
        <div class="col-10 col-md-5 col-lg-5 formulario">
            <div class="mb-3 logo">
                <img src="/img/logo.png" alt="Logo" class="img-fluid">
            </div>

            <div class="mb-4 text-center">
                <h2 class="titulo">Iniciar Sesión</h2>
                <p>Ingresa a la Plataforma Virtual</p>
            </div>
            <form id="frmLogin">
                <div class="mb-3">
                  <label for="txtCorreo" class="form-label">Usuario</label>
                  <input type="email" name="correo" class="form-control form-control-lg" id="txtCorreo">                      
                </div>

                <div class="mb-2">
                  <label for="txtPassword" class="form-label">Contraseña</label>
                  <input type="password" name="password" class="form-control form-control-lg" id="txtPassword">
                </div>
                <div class="mb-2">
                  <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" name="recordar" id="customCheck1">
                    <label class="custom-control-label" for="customCheck1">Recordarme</label>
                  </div>
                </div>
                <div class="mb-3">
                  {{-- <a href="#" class="text-center d-block enlace-olvide">¿Olvidaste tu contraseña?</a> --}}
                </div>
                <button type="submit" class="btn btn-primary w-100 btn-lg">Acceder</button>
              </form>
        </div>
    </div>
</div>
</body>
</html>