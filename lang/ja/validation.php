<?php

return [

    /*
    |--------------------------------------------------------------------------
    | バリデーション言語行
    |--------------------------------------------------------------------------
    |
    | 以下の言語行には、バリデータクラスによって使用されるデフォルトのエラーメッセージが含まれています。
    | これらのルールの一部には、サイズルールなどの複数のバージョンがあります。
    | ここでこれらのメッセージを自由に調整してください。
    |
    */

    'accepted' => ':attribute を承認する必要があります。',
    'accepted_if' => ':other が :value の場合、:attribute を承認する必要があります。',
    'active_url' => ':attribute は有効なURLである必要があります。',
    'after' => ':attribute は :date より後の日付である必要があります。',
    'after_or_equal' => ':attribute は :date 以降の日付である必要があります。',
    'alpha' => ':attribute は文字のみを含む必要があります。',
    'alpha_dash' => ':attribute は文字、数字、ダッシュ、アンダースコアのみを含む必要があります。',
    'alpha_num' => ':attribute は文字と数字のみを含む必要があります。',
    'array' => ':attribute は配列である必要があります。',
    'ascii' => ':attribute はシングルバイトの英数字と記号のみを含む必要があります。',
    'before' => ':attribute は :date より前の日付である必要があります。',
    'before_or_equal' => ':attribute は :date 以前の日付である必要があります。',
    'between' => [
        'array' => ':attribute のアイテム数は :min から :max の間である必要があります。',
        'file' => ':attribute のサイズは :min から :max キロバイトの間である必要があります。',
        'numeric' => ':attribute は :min から :max の間である必要があります。',
        'string' => ':attribute の文字数は :min から :max の間である必要があります。',
    ],
    'boolean' => ':attribute は true または false である必要があります。',
    'can' => ':attribute は不正な値を含んでいます。',
    'confirmed' => ':attribute の確認が一致しません。',
    'current_password' => 'パスワードが正しくありません。',
    'date' => ':attribute は有効な日付である必要があります。',
    'date_equals' => ':attribute は :date と同じ日付である必要があります。',
    'date_format' => ':attribute は :format 形式と一致する必要があります。',
    'decimal' => ':attribute は :decimal 小数点以下の桁数である必要があります。',
    'declined' => ':attribute は辞退する必要があります。',
    'declined_if' => ':other が :value の場合、:attribute は辞退する必要があります。',
    'different' => ':attribute と :other は異なる必要があります。',
    'digits' => ':attribute は :digits 桁である必要があります。',
    'digits_between' => ':attribute は :min 桁から :max 桁の間である必要があります。',
    'dimensions' => ':attribute の画像サイズが無効です。',
    'distinct' => ':attribute に重複する値があります。',
    'doesnt_end_with' => ':attribute は次のいずれかで終わってはなりません: :values。',
    'doesnt_start_with' => ':attribute は次のいずれかで始まってはなりません: :values。',
    'email' => ':attribute は有効なメールアドレスである必要があります。',
    'ends_with' => ':attribute は次のいずれかで終わる必要があります: :values。',
    'enum' => '選択された :attribute は無効です。',
    'exists' => '正しい :attribute を選択してください。',
    'extensions' => ':attribute は次の拡張子のいずれかである必要があります: :values。',
    'file' => ':attribute はファイルである必要があります。',
    'filled' => ':attribute には値が必要です。',
    'gt' => [
        'array' => ':attribute には :value 個以上のアイテムが必要です。',
        'file' => ':attribute は :value キロバイトを超える必要があります。',
        'numeric' => ':attribute は :value を超える必要があります。',
        'string' => ':attribute は :value 文字を超える必要があります。',
    ],
    'gte' => [
        'array' => ':attribute には :value 個以上のアイテムが必要です。',
        'file' => ':attribute は :value キロバイト以上である必要があります。',
        'numeric' => ':attribute は :value 以上である必要があります。',
        'string' => ':attribute は :value 文字以上である必要があります。',
    ],
    'hex_color' => ':attribute は有効な16進数の色である必要があります。',
    'image' => ':attribute は画像である必要があります。',
    'in' => '選択された :attribute は無効です。',
    'in_array' => ':attribute は :other に存在する必要があります。',
    'integer' => ':attribute は整数である必要があります。',
    'ip' => ':attribute は有効なIPアドレスである必要があります。',
    'ipv4' => ':attribute は有効なIPv4アドレスである必要があります。',
    'ipv6' => ':attribute は有効なIPv6アドレスである必要があります。',
    'json' => ':attribute は有効なJSON文字列である必要があります。',
    'lowercase' => ':attribute は小文字である必要があります。',
    'lt' => [
        'array' => ':attribute には :value 個未満のアイテムが必要です。',
        'file' => ':attribute は :value キロバイト未満である必要があります。',
        'numeric' => ':attribute は :value 未満である必要があります。',
        'string' => ':attribute は :value 文字未満である必要があります。',
    ],
    'lte' => [
        'array' => ':attribute には :value 個以下のアイテムが必要です。',
        'file' => ':attribute は :value キロバイト以下である必要があります。',
        'numeric' => ':attribute は :value 以下である必要があります。',
        'string' => ':attribute は :value 文字以下である必要があります。',
    ],
    'mac_address' => ':attribute は有効なMACアドレスである必要があります。',
    'max' => [
        'array' => ':attribute には :max 個以下のアイテムが必要です。',
        'file' => ':attribute は :max キロバイト以下である必要があります。',
        'numeric' => ':attribute は :max 以下を入力してください。',
        'string' => ':attribute は :max 文字以内で入力してください。',
    ],
    'max_digits' => ':attribute は :max 桁以下である必要があります。',
    'mimes' => ':attribute は次のタイプのファイルである必要があります: :values。',
    'mimetypes' => ':attribute は次のタイプのファイルである必要があります: :values。',
    'min' => [
        'array' => ':attribute には少なくとも :min 個のアイテムが必要です。',
        'file' => ':attribute は少なくとも :min キロバイトである必要があります。',
        'numeric' => ':attribute は :min 以上を入力してください。',
        'string' => ':attribute は :min 文字以上を入力してください。',
    ],
    'min_digits' => ':attribute は少なくとも :min 桁である必要があります。',
    'missing' => ':attribute は存在しない必要があります。',
    'missing_if' => ':other が :value の場合、:attribute は存在しない必要があります。',
    'missing_unless' => ':other が :value でない限り、:attribute は存在しない必要があります。',
    'missing_with' => ':values が存在する場合、:attribute は存在しない必要があります。',
    'missing_with_all' => ':values が存在する場合、:attribute は存在しない必要があります。',
    'multiple_of' => ':attribute は :value の倍数である必要があります。',
    'not_in' => '選択された :attribute は無効です。',
    'not_regex' => ':attribute の形式が無効です。',
    'numeric' => ':attribute は数値で入力してください。',
    'password' => [
        'letters' => ':attribute には少なくとも1文字が含まれている必要があります。',
        'mixed' => ':attribute には少なくとも1つの大文字と1つの小文字が含まれている必要があります。',
        'numbers' => ':attribute には少なくとも1つの数字が含まれている必要があります。',
        'symbols' => ':attribute には少なくとも1つの記号が含まれている必要があります。',
        'uncompromised' => '指定された :attribute はデータ漏洩に含まれています。別の :attribute を選択してください。',
    ],
    'present' => ':attribute は存在する必要があります。',
    'present_if' => ':other が :value の場合、:attribute は存在する必要があります。',
    'present_unless' => ':other が :value でない限り、:attribute は存在する必要があります。',
    'present_with' => ':values が存在する場合、:attribute は存在する必要があります。',
    'present_with_all' => ':values が存在する場合、:attribute は存在する必要があります。',
    'prohibited' => ':attribute は禁止されています。',
    'prohibited_if' => ':other が :value の場合、:attribute は禁止されています。',
    'prohibited_unless' => ':other が :values の中にない限り、:attribute は禁止されています。',
    'prohibits' => ':attribute は :other が存在することを禁止します。',
    'regex' => ':attribute の形式が無効です。',
    'required' => ':attribute は必須入力です。',
    'required_array_keys' => ':attribute には次の項目が含まれている必要があります: :values。',
    'required_if' => ':other が :value の場合、:attribute は必須です。',
    'required_if_accepted' => ':other が承認されている場合、:attribute は必須です。',
    'required_unless' => ':other が :values の中にない限り、:attribute は必須です。',
    'required_with' => ':values が存在する場合、:attribute は必須です。',
    'required_with_all' => ':values が存在する場合、:attribute は必須です。',
    'required_without' => ':values が存在しない場合、:attribute は必須です。',
    'required_without_all' => ':values がすべて存在しない場合、:attribute は必須です。',
    'same' => ':attribute と :other は一致する必要があります。',
    'size' => [
        'array' => ':attribute は :size 個のアイテムを含む必要があります。',
        'file' => ':attribute は :size キロバイトである必要があります。',
        'numeric' => ':attribute は :size である必要があります。',
        'string' => ':attribute は :size 文字である必要があります。',
    ],
    'starts_with' => ':attribute は次のいずれかで始まる必要があります: :values。',
    'string' => ':attribute は文字列である必要があります。',
    'timezone' => ':attribute は有効なタイムゾーンである必要があります。',
    'unique' => ':attribute はすでに登録されています。',
    'uploaded' => ':attribute のアップロードに失敗しました。',
    'uppercase' => ':attribute は大文字である必要があります。',
    'url' => ':attribute は有効なURLである必要があります。',
    'ulid' => ':attribute は有効なULIDである必要があります。',
    'uuid' => ':attribute は有効なUUIDである必要があります。',

    /*
    |--------------------------------------------------------------------------
    | カスタムバリデーション言語行
    |--------------------------------------------------------------------------
    |
    | ここでは、特定の属性ルールに対してカスタムバリデーションメッセージを指定できます。
    | "attribute.rule"の命名規則を使用して行を指定します。
    | これにより、特定の属性ルールに対して迅速にカスタム言語行を指定できます。
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'カスタムメッセージ',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | カスタムバリデーション属性
    |--------------------------------------------------------------------------
    |
    | 以下の言語行は、属性プレースホルダーを「email」の代わりに「Eメールアドレス」などの
    | 読みやすいものに置き換えるために使用されます。これにより、メッセージがより表現力豊かになります。
    |
    */
    # キー名も日本語に変更
    'attributes' => [
        'category_id' => 'カテゴリ',
        'title' => 'タイトル',
        'price' => '価格',
        'author_ids' => '著者',
        'author_ids.*' => '著者',
    ],

];
