<?php

namespace App\Providers;

// Trouve l'image d'un projet dans public/img (project-{id}.jpg, png ou webp).
class Image
{
    public static function project(int $projectId): ?string
    {
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $extension) {
            $file = "img/project-$projectId.$extension";

            if (file_exists(dirname(__DIR__) . '/public/' . $file)) {
                return ASSET . '/' . $file;
            }
        }

        return null;
    }

    // Ajoute une clé image à chaque projet d'une liste.
    public static function addToProjects(array $projects): array
    {
        foreach ($projects as $index => $project) {
            $projects[$index]['image'] = self::project((int) $project['id']);
        }

        return $projects;
    }
}
