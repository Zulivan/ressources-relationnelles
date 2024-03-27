import 'package:flutter/material.dart';
import 'dart:convert';
import '../apimodule.dart';

class Person {
  final String nom;
  final String prenom;
  final String dateNaissance;
  final String email;
  final String adresse;
  final String ville;
  final String codePostal;
  final String password;

  Person({
    required this.nom,
    required this.prenom,
    required this.dateNaissance,
    required this.email,
    required this.adresse,
    required this.ville,
    required this.codePostal,
    required this.password,
  });
}

class InscriptionScreen extends StatefulWidget {
  const InscriptionScreen({super.key});

  @override
  InscriptionScreenState createState() => InscriptionScreenState();
}

class InscriptionScreenState extends State<InscriptionScreen> {
  final nomController = TextEditingController();
  final prenomController = TextEditingController();
  final dateNaissanceController = TextEditingController();
  final emailController = TextEditingController();
  final adresseController = TextEditingController();
  final villeController = TextEditingController();
  final codePostalController = TextEditingController();
  final passwordController = TextEditingController();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Inscription'),
      ),
      body: SingleChildScrollView(
        child: Padding(
          padding: const EdgeInsets.all(16.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              TextFormField(
                controller: nomController,
                decoration: const InputDecoration(
                  labelText: 'Nom',
                ),
              ),
              const SizedBox(height: 8.0),
              TextFormField(
                controller: prenomController,
                decoration: const InputDecoration(
                  labelText: 'Prénom',
                ),
              ),
              const SizedBox(height: 8.0),
              TextFormField(
                controller: dateNaissanceController,
                decoration: const InputDecoration(
                  labelText: 'Date de naissance',
                ),
              ),
              const SizedBox(height: 8.0),
              TextFormField(
                controller: emailController,
                decoration: const InputDecoration(
                  labelText: 'Email',
                ),
              ),
              const SizedBox(height: 8.0),
              TextFormField(
                controller: adresseController,
                decoration: const InputDecoration(
                  labelText: 'Adresse',
                ),
              ),
              const SizedBox(height: 8.0),
              Row(
                children: [
                  Expanded(
                    child: TextFormField(
                      controller: villeController,
                      decoration: const InputDecoration(
                        labelText: 'Ville',
                      ),
                    ),
                  ),
                  const SizedBox(width: 8.0),
                  Expanded(
                    child: TextFormField(
                      controller: codePostalController,
                      decoration: const InputDecoration(
                        labelText: 'Code postal',
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 8.0),
              TextFormField(
                controller: passwordController,
                obscureText: true,
                decoration: const InputDecoration(
                  labelText: 'Mot de passe',
                ),
              ),
              const SizedBox(height: 16.0),
              ElevatedButton(
                child: const Text('S\'inscrire'),
                onPressed: () async {
                  Person nouvellePersonne = Person(
                    nom: nomController.text,
                    prenom: prenomController.text,
                    dateNaissance: dateNaissanceController.text,
                    email: emailController.text,
                    adresse: adresseController.text,
                    ville: villeController.text,
                    codePostal: codePostalController.text,
                    password: passwordController.text,
                  );

                  final response = await ApiModule.request('api/signup', 'POST',
                    body: jsonEncode({
                      'nom': nouvellePersonne.nom,
                      'prenom': nouvellePersonne.prenom,
                      'date_naissance': nouvellePersonne.dateNaissance,
                      'email': nouvellePersonne.email,
                      'adresse': nouvellePersonne.adresse,
                      'ville': nouvellePersonne.ville,
                      'code_postal': nouvellePersonne.codePostal,
                      'password': nouvellePersonne.password,
                    }),
                  );

                  final responseData = response.data;
                  if (responseData['message'].contains('User created!')) {
                    // ScaffoldMessenger.of(context).showSnackBar(
                    //   const SnackBar(content: Text('Inscription réussie !')),
                    // );
                    // Navigator.pop(context);
                  } else {
                    // ScaffoldMessenger.of(context).showSnackBar(
                    //   SnackBar(
                    //     content: const Text('Erreur lors de l\'inscription'),
                    //     action: SnackBarAction(
                    //       label: 'Détails',
                    //       onPressed: () {
                    //         showDialog(
                    //           context: context,
                    //           builder: (context) {
                    //             // Parse the JSON string into a map/dictionary
                    //             final responseBody = response.data;
                    //             if (responseBody != null && responseBody.containsKey('message')) {
                    //               final message = responseBody['message'];
                    //               return AlertDialog(
                    //                 title: const Text('Détails de l\'erreur'),
                    //                 content: Text('$message'),
                    //                 actions: [
                    //                   TextButton(
                    //                     onPressed: () => Navigator.of(context).pop(),
                    //                     child: const Text('OK'),
                    //                   ),
                    //                 ],
                    //               );
                    //             }
                    //             // Fallback dialog if 'message' is not available or responseBody is null
                    //             return AlertDialog(
                    //               title: const Text('Détails de l\'erreur'),
                    //               content: Text('${response.data}'),
                    //               actions: [
                    //                 TextButton(
                    //                   onPressed: () => Navigator.of(context).pop(),
                    //                   child: const Text('OK'),
                    //                 ),
                    //               ],
                    //             );
                    //           },
                    //         );
                    //       },
                    //     ),
                    //   ),
                    // );
                  }
                },
              ),
            ],
          ),
        ),
      ),
    );
  }
}