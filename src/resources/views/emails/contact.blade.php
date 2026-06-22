<!DOCTYPE html>
<html>
<head>
    <title>Обратная связь</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">

@if($isAdminCopy)
<h2>Данные обращения</h2>

<p><strong>Имя:</strong> {{ $data['name'] }}</p>
<p><strong>Телефон:</strong> {{ $data['phone'] }}</p>
<p><strong>Email:</strong> {{ $data['email'] }}</p>
<p><strong>Комментарий:</strong> {{ $data['comment'] }}</p>

@if(!empty($data['ai_analysis']))
<hr style="border: 0; border-top: 1px solid #ccc; margin: 20px 0;">
<h3>Автоматический AI-анализ (Для внутреннего использования)</h3>
<p><strong>Категория:</strong> {{ $data['ai_analysis']['category'] ?? 'Не определена' }}</p>
<p><strong>Тональность:</strong> {{ $data['ai_analysis']['sentiment'] ?? 'Не определена' }}</p>
@endif
@endif

@if(!empty($data['ai_analysis']['suggestedReply']))
<h3 style="color: #2c3e50;">Сгенерированный AI ответ пользователю:</h3>
<div style="background: #f8f9fa; padding: 15px; border-left: 4px solid #3498db;">
    {!! nl2br(e($data['ai_analysis']['suggestedReply'])) !!}
</div>
@endif

</body>
</html>
