<DOCTYPE html>
<html lang="es">
<form action="/admin/maquinarias" method="POST" enctype="multipart/form-data">
    @csrf
    <label>Nombre:</label>
    <input type="text" name="nombre">
    
    <label>Descripción:</label>
    <textarea name="descripcion"></textarea>
    
    <label>Precio:</label>
    <input type="number" step="0.01" name="precio">
    
    <label>Imagen:</label>
    <input type="file" name="imagen">

    <button type="submit">Guardar Maquinaria</button>
</form>
</html>