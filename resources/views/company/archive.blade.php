//				// Получаем все файлы в директории storage/app/public
//				$files = File::allFiles(storage_path('app/public/archive'));
//
//				// Инициализируем пустой массив для хранения URL файлов
//				$fileUrls = [];
//
//				// Перебираем каждый файл и получаем его URL
//				foreach ($files as $file) {
//					// Получаем путь к файлу относительно public директории
//					$filePath = 'storage/app/public' . str_replace(storage_path('app/public'), '', $file);
//
//					// Генерируем URL файла
//					$fileUrl = asset($filePath);
//
//					// Добавляем URL в массив
//					$fileUrls[] = $fileUrl;
//				}
//				Telegram::sendMessage([
//					'chat_id' => $req -> message -> chat -> id,
//					'text' => (string)json_encode($fileUrls)
//				]);
