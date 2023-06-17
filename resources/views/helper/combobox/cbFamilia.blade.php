<select name="id_familia" id="{{$idFamilia}}" data-placeholder="Seleccione una familia" required class="select2-simple">
    <option value=""></option>
    @foreach ($familias as $familia)
        <option value="{{$familia->id}}">{{$familia->codigo .' - ' . $familia->nombre}}</option>
    @endforeach
</select>