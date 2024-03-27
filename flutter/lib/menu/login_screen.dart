import 'package:flutter/material.dart';

class LoginScreen extends StatefulWidget {
  final Future<bool> Function(String, String) onLogin;

  const LoginScreen({super.key, required this.onLogin});

  @override
  LoginScreenState createState() => LoginScreenState();
}

class LoginScreenState extends State<LoginScreen> {
  final nomUtilisateurController = TextEditingController();
  final motDePasseController = TextEditingController();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Connexion'),
      ),
      body: Container(
        padding: const EdgeInsets.all(16.0),
        child: ListView(
          children: [
            TextFormField(
              controller: nomUtilisateurController,
              decoration: const InputDecoration(
                labelText: 'Adresse e-mail',
                border: OutlineInputBorder(),
              ),
            ),
            const SizedBox(height: 16.0),
            TextFormField(
              controller: motDePasseController,
              obscureText: true,
              decoration: const InputDecoration(
                labelText: 'Mot de passe',
                border: OutlineInputBorder(),
              ),
            ),
            const SizedBox(height: 16.0),
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.blue, // Update with your desired button color
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8.0),
                ),
              ),
              child: const Text(
                'Se connecter',
                style: TextStyle(
                  fontSize: 16.0,
                  fontWeight: FontWeight.bold,
                  color: Colors.white,
                ),
              ),
              onPressed: () {
                _handleLogin(context);
              },
            ),
          ],
        ),
      ),
    );
  }

Future<void> _handleLogin(BuildContext context) async {
  // final currentContext = context;

  bool estConnecte = await widget.onLogin(
    nomUtilisateurController.text,
    motDePasseController.text,
  );

  if (estConnecte) {
    // ScaffoldMessenger.of(currentContext).showSnackBar(
    //   const SnackBar(content: Text('Connexion réussie !')),
    // );
    // Navigator.pop(currentContext);
  } else {
    // TODO SCAFFOLD
    // ScaffoldMessenger.of(currentContext).showSnackBar( 
    //   const SnackBar(
    //     content: Text(
    //       'Erreur de connexion. Veuillez vérifier vos informations d\'identification.',
    //     ),
    //   ),
    // );
  }
}
}