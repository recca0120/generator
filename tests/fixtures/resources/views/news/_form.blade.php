<div class="item form-group">
    <label for="id" class="control-label">
        Id
        <span class="required">*</span>
    </label>
    {{ Form::text('id', $news->id, [
        'class' => 'form-control',
        'required' => 'required',
    ]) }}
</div>
