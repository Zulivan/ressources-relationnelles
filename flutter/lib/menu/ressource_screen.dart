
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../models/ressource.dart';
import '../providers/user.dart';
import '../models/commentaire.dart';

import '../apimodule.dart';

import 'package:flutter_html/flutter_html.dart';

class RessourceScreen extends StatefulWidget {
  final int ressourceId;

  const RessourceScreen({super.key, 
    required this.ressourceId
  });

  @override
  RessourceScreenState createState() => RessourceScreenState();
}

class RessourceScreenState extends State<RessourceScreen> {
  //final commentFormKey = GlobalKey<FormState>();
  Ressource? ressourceData;
  String headerReponse = '';
  bool isLoading = false;

  final messageController = TextEditingController();
  int? commentaireARepondre;

  @override
  void initState() {
    super.initState();
    getRessource();
    getNom();
  }

  void commenter() async {
    const url = 'api/ressource_comment';
    final body = {
      'message': messageController.text,
      'ressource': widget.ressourceId,
      'reponse': commentaireARepondre,
    };

    try {
      final response = await ApiModule.request(url, 'POST', body: body);
      if (response.statusCode == 201) {
        messageController.clear();
        commentaireARepondre = null;

        final data = response.data;
        await getRessource();
        if (data['message'] != null) {
            setState(() {
            headerReponse = data['message'];
            });
        }
      } else {
        final error = response.data;
        setState(() {
          headerReponse = error['message'];
        });
      }
    } catch (e) {
      // print('Exception: $e');
      setState(() {
        headerReponse = '$e';
      });
    }
  }

  Future<void> validerRessource() async {
    final url = 'api/ressources/${widget.ressourceId}/valider';

    try {
      final response = await ApiModule.request(url, 'PUT');
      if (response.statusCode == 200) {
        await getRessource();
      }
    } catch (e) {
      // print('Exception: $e');
    }
  }

  Future<void> supprimerCommentaire(dynamic commentaire) async {
    // print('supprimerCommentaire');
    final id = commentaire.id;
    final url = 'api/ressource_comment/$id';

    try {
      final response = await ApiModule.request(url, 'DELETE');
      if (response.statusCode == 201) {
        await getRessource();
      }
    } catch (e) {
      // print('Exception: $e');
    }
  }

  Future<void> getRessource() async {
    setState(() {
      isLoading = true;
    });

    final id = widget.ressourceId;
    final url = 'api/ressources/$id';

    try {
      final response = await ApiModule.request(url, 'GET');
      // print('response.statusCode: ${response.statusCode}');
      if (response.statusCode == 200) {
        final data = response.data;

        setState(() {
          ressourceData = Ressource.fromJson(data);
        });
        
      } else {
        // print('Error: ${response.statusCode}');
      }
    } catch (e) {
      // print('Exception: $e');
    } finally {
      setState(() {
        isLoading = false;
      });
    }
  }

  Future<void> getNom() async {
    // final userProvider = Provider.of<UserProvider>(context);
    // final user = userProvider.getUser();
//    setState(() {
//       this.user = user ?? null;
//    });
  }

  void setReponse(Commentaire reponse) {
    setState(() {
        headerReponse = 'Répondre au commentaire de ${reponse.utilisateur.prenom} ${reponse.utilisateur.nom}';
    });
    setState(() {
        commentaireARepondre = reponse.id;
    });
  }

  @override
  Widget build(BuildContext context) {
    final userProvider = Provider.of<UserProvider>(context);
    final user = userProvider.getUser();

    final isModerator = user?.moderator ?? false;
    final userId = user?.id ?? 0;
    final nbCommentaires = ressourceData?.nbCommentaires ?? 0;
    final nbFavoris = ressourceData?.nbFavoris ?? 0;

    return Scaffold(
    appBar: AppBar(
        title: Text(ressourceData?.titre ?? 'Chargement...'),
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (ressourceData != null && ressourceData?.validee == false)
              Container(
                color: Colors.yellow,
                child: const Padding(
                  padding: EdgeInsets.all(16.0),
                  child: Text(
                    'Cette ressource n\'est pas encore validée par un modérateur. Elle n\'est donc pas visible par les autres utilisateurs.',
                    style: TextStyle(
                      color: Colors.black,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ),
            if (ressourceData == null)
              const Padding(
                padding: EdgeInsets.all(16.0),
                child: Text('Chargement de la ressource en cours...'),
              ),
            if (ressourceData != null && ressourceData?.titre != null) ...[
              Padding(
                padding: const EdgeInsets.all(16.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      ressourceData?.titre ?? '',
                      style: const TextStyle(
                        fontSize: 24,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    Text(ressourceData?.typeRessource.libelle ?? ''),
                    const SizedBox(height: 16),
                    Text('Publié le ${ressourceData?.dateCreation}'),
                    const SizedBox(height: 16),
                    // Row(
                    //   children: [
                    //     CircleAvatar(
                    //       child: Icon(Icons.person),
                    //     ),
                    //     SizedBox(width: 8),
                    //     Text(
                    //       '${ressourceData?.createur.prenom} ${ressourceData?.createur.nom}',
                    //     ),
                    //   ],
                    // ),
                  ],
                ),
              ),
              Padding(
                padding: const EdgeInsets.all(16.0),
                child: Html(data: ressourceData?.document ?? ''),
              ),
              Padding(
                padding: const EdgeInsets.all(16.0),
                child: Text(ressourceData?.texte ?? ''),
              ),
              Padding(
                padding: const EdgeInsets.all(16.0),
                child: Row(
                  children: [
                    for (var categorie in ressourceData?.categories ?? [])
                      TextButton(
                        onPressed: () {},
                        child: Text(categorie.libelle),
                      ),
                  ],
                ),
              ),
              if (ressourceData?.nbFavoris != null ||
                  ressourceData?.nbCommentaires != null)
                Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Row(
                    children: [
                      if (ressourceData?.nbFavoris != null)
                        IconButton(
                          onPressed: () {},
                          icon: const Icon(Icons.favorite),
                          tooltip: nbFavoris == 1
                              ? '$nbFavoris personne aime cette ressource'
                              : '$nbFavoris personnes aiment cette ressource',
                        ),
                      if (ressourceData?.nbCommentaires != null)
                        IconButton(
                          onPressed: () {},
                          icon: const Icon(Icons.comment),
                          tooltip: nbCommentaires == 1
                              ? '$nbCommentaires commentaire'
                              : '$nbCommentaires commentaires',
                        ),
                    ],
                  ),
                ),
              if (isModerator)
                Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text('Espace modération'),
                      const SizedBox(height: 16),
                      if (ressourceData != null &&
                          ressourceData?.validee == false)
                        ElevatedButton(
                          onPressed: () {
                            validerRessource();
                          },
                          child: const Text('Valider cette ressource'),
                        ),
                    ],
                  ),
                ),
              const Padding(
                padding: EdgeInsets.all(16.0),
                child: Text('Commentaires'),
              ),
              if (ressourceData != null && ressourceData?.titre != null)
                ...[
                  for (var commentaire in ressourceData?.commentaires ?? []) ...[
                    Padding(
                      padding: const EdgeInsets.all(16.0),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              Text(
                                '${commentaire.utilisateur?.prenom} ${commentaire.utilisateur?.nom}',
                                style: const TextStyle(
                                   fontSize: 17
                                ),
                              ),
                              if (commentaire.utilisateur.id == userId || isModerator)
                                IconButton(
                                  onPressed: () {
                                    supprimerCommentaire(commentaire);
                                  },
                                  icon: const Icon(Icons.delete),
                                  tooltip: 'Supprimer le commentaire',
                                ),
                            ],
                          ),
                          Text(commentaire.text,
                            style: const TextStyle(
                              fontSize: 15
                            )),
                          for (var reponse in commentaire.reponses)
                            Padding(
                              padding: const EdgeInsets.only(left: 16.0, top: 8),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    '${reponse.utilisateur.prenom} ${reponse.utilisateur.nom} a répondu :',
                                    style: const TextStyle(
                                        fontSize: 14
                                    ),
                                  ),
                                  Text(reponse.text,
                                    style: const TextStyle(
                                        fontSize: 16
                                    ),
                                ),
                                ],
                              ),
                            ),
                          if (user != null)
                            ElevatedButton(
                              onPressed: () {
                                setReponse(commentaire);
                              },
                              child: const Text('Répondre'),
                            ),
                        ],
                      ),
                    ),
                  ],
                  if (user != null)
                    Padding(
                      padding: const EdgeInsets.all(16.0),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Divider(),
                          Text(headerReponse),
                          const SizedBox(height: 16),
                          TextField(
                                controller: messageController,
                                decoration: const InputDecoration(
                                   labelText: 'Votre commentaire',
                                ),
                            ),
                          ElevatedButton(
                            onPressed: () {
                              commenter();
                            },
                            child: const Text('Publier le commentaire'),
                          ),
                        ],
                      ),
                    ),
                ],
            ],
          ],
        ),
      ),
    );
  }
}