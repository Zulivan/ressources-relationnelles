class User {
  final int id;
  final bool moderator;
  final bool administrator;
  final bool superAdministrator;
  final String nom;
  final String prenom;
  final String email;

  User({
    required this.id,
    required this.moderator,
    required this.administrator,
    required this.superAdministrator,
    required this.nom,
    required this.prenom,
    required this.email,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    // print(json);
    return User(
      id: json['id'],
      moderator: json['moderator'],
      administrator: json['administrator'],
      superAdministrator: json['super_administrator'],
      nom: json['nom'],
      prenom: json['prenom'],
      email: json['email'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'moderator': moderator,
      'administrator': administrator,
      'super_administrator': superAdministrator,
      'nom': nom,
      'prenom': prenom,
      'email': email,
    };
  }
}