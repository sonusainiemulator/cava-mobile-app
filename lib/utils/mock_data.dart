final List<Map<String, dynamic>> mockAds = [
  {'title': 'Vino Tinto Reserva', 'image_path': 'assets/ad_wine1.jpg', 'description': 'Special 20% off at La Europea. ideal for red meat.', 'location': 'CDMX, Polanco'},
  {'title': 'Casa Madero 3V', 'image_path': 'assets/ad_wine2.jpg', 'description': 'Buy 2 get 1 free. The pride of Parras, Coahuila.', 'location': 'Monterrey, NL'},
  {'title': 'Mezcal Amores Banksy', 'image_path': 'assets/ad_mezcal.jpg', 'description': 'Limited edition art bottle. Available now.', 'location': 'Oaxaca Centro'},
  {'title': 'Tequila Don Julio 1942', 'image_path': 'assets/ad_tequila.jpg', 'description': 'Exclusive tasting event this Friday.', 'location': 'Guadalajara, Jal'},
  {'title': 'Champagne Moët', 'image_path': 'assets/ad_champa.jpg', 'description': 'Celebrate with style. New year discounts.', 'location': 'Cancún, QR'},
];

final List<Map<String, dynamic>> mockBeverages = [
  {'barcode':'7501000123001','name':'Don Julio 70','brand':'Don Julio','presentation':'700 ml','alcohol_degrees':35.0,'price':1200.0},
  {'barcode':'7501000123002','name':'Maestro Dobel Diamante','brand':'Maestro Dobel','presentation':'750 ml','alcohol_degrees':35.0,'price':750.0},
  {'barcode':'7501000123003','name':'Reserva de la Familia','brand':'Jose Cuervo','presentation':'750 ml','alcohol_degrees':38.0,'price':2800.0},
  {'barcode':'7501000123004','name':'Mezcal 400 Conejos','brand':'400 Conejos','presentation':'750 ml','alcohol_degrees':38.0,'price':450.0},
  {'barcode':'7501000123005','name':'Herradura Reposado','brand':'Herradura','presentation':'700 ml','alcohol_degrees':40.0,'price':650.0},
  {'barcode':'7501000123006','name':'Montelobos Espadín','brand':'Montelobos','presentation':'750 ml','alcohol_degrees':43.2,'price':890.0},
  {'barcode':'7501000123007','name':'Clase Azul Reposado','brand':'Clase Azul','presentation':'750 ml','alcohol_degrees':40.0,'price':3500.0},
  {'barcode':'7501000123008','name':'1800 Cristalino','brand':'1800','presentation':'700 ml','alcohol_degrees':35.0,'price':800.0},
];

final List<Map<String, dynamic>> mockReviews = [
  {'username': 'TequilaLover99', 'content': 'Fantastic smoothness, notes of vanilla and oak.', 'rating': 5.0, 'date': '2023-10-15', 'approved': 1},
  {'username': 'AgaveMaster', 'content': 'A bit too sweet for my taste, but good for mixing.', 'rating': 3.5, 'date': '2023-10-12', 'approved': 1},
  {'username': 'SofiaR', 'content': 'Best mezcal I have tried in years. Smoky perfection.', 'rating': 5.0, 'date': '2023-10-10', 'approved': 1},
  {'username': 'CarlosM', 'content': 'Great value for the price.', 'rating': 4.0, 'date': '2023-10-05', 'approved': 1},
  {'username': 'AnaG', 'content': 'Beautiful bottle, amazing taste.', 'rating': 5.0, 'date': '2023-09-28', 'approved': 1},
];

final List<Map<String, dynamic>> mockEvents = [
  {'title': 'Tequila & Taces Festival', 'date': '2026-03-15', 'location': 'Parque Bicentenario, CDMX', 'description': 'Street food and premium tequila tasting under the stars.', 'image_path': ''},
  {'title': 'Mezcal Masterclass', 'date': '2026-04-02', 'location': 'La Mezcalería, Guadalajara', 'description': 'Learn from the maestros about the artisanal process.', 'image_path': ''},
  {'title': 'Agave Harvest Tour', 'date': '2026-05-10', 'location': 'Casa Herradura, Amatitán', 'description': 'Full day tour in the agave fields with lunch included.', 'image_path': ''},
  {'title': 'Cocktail Night', 'date': '2026-02-14', 'location': 'Rooftop Bar 360', 'description': 'Valentine\'s special: 2x1 on tequila cocktails.', 'image_path': ''},
];

final List<Map<String, dynamic>> mockChats = [
  {'counterpart': 'Soporte TequilaApp', 'last_message': 'Hola, ¿en qué podemos ayudarte?', 'last_message_time': '2026-01-30T09:00:00'},
  {'counterpart': 'Juan Pérez', 'last_message': '¿Probaste el nuevo Dobel?', 'last_message_time': '2026-01-28T18:30:00'},
  {'counterpart': 'Maria Mezcal', 'last_message': 'Nos vemos en la cata mañana', 'last_message_time': '2026-01-25T14:20:00'},
];
