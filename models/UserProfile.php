<?php

class UserProfile
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    /* ------------ Helpers ------------ */
    private function fetchOne(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            $row = $stmt->fetch();
            return $row ?: [];
        } catch (PDOException $e) {
            if (in_array($e->getCode(), ['2006', '2013'])) { // server gone away / lost connection
                $this->conn = Database::reconnect()->getConnection();
                $stmt = $this->conn->prepare($sql);
                $stmt->execute($params);
                $row = $stmt->fetch();
                return $row ?: [];
            }
            throw $e;
        }
    }

    private function execute(string $sql, array $params = []): bool
    {
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            if (in_array($e->getCode(), ['2006', '2013'])) {
                $this->conn = Database::reconnect()->getConnection();
                $stmt = $this->conn->prepare($sql);
                return $stmt->execute($params);
            }
            throw $e;
        }
    }

    public function getConnection(): PDO { return $this->conn; }

    /* ------------ Account Settings ------------ */
    public function updateEmail(int $userId, string $email): bool
    {
        $sql = "UPDATE users SET email = ? WHERE user_id = ?";
        return $this->execute($sql, [$email, $userId]);
    }

    public function updatePassword(int $userId, string $hashedPassword): bool
    {
        $sql = "UPDATE users SET password = ? WHERE user_id = ?";
        return $this->execute($sql, [$hashedPassword, $userId]);
    }

    public function deleteUser(int $userId): bool
    {
        // Delete dependent records first (if foreign keys don't cascade)
        // Assuming CASCADE is set up or we rely on DB integrity, but explicitly deleting helps clarity
        // For now, just deleting user should trigger cascades if set, or leave orphans if not. 
        // Best effort: delete user.
        $sql = "DELETE FROM users WHERE user_id = ?";
        return $this->execute($sql, [$userId]);
    }

    /* ------------ Personal Info ------------ */
    public function updateUserCore(int $userId, string $firstName, string $lastName, string $phone): bool
    {
        $sql = "UPDATE users SET first_name = ?, last_name = ?, phone = ? WHERE user_id = ?";
        return $this->execute($sql, [$firstName, $lastName, $phone, $userId]);
    }

    public function getUser(int $userId): array
    {
        return $this->fetchOne("SELECT * FROM users WHERE user_id = ? LIMIT 1", [$userId]);
    }

    public function getUserPublicInfo(int $userId): array
    {
        return $this->fetchOne("SELECT * FROM user_public_info WHERE user_id = ? LIMIT 1", [$userId]);
    }

    public function getAchievements(int $userId): array
    {
        $sql = "SELECT a.achievement_name
                  FROM user_achievements ua
                  JOIN achievements a ON ua.achievement_id = a.achievement_id
                 WHERE ua.user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll() ?: [];
    }

    public function getSkills(int $userId): array
    {
        $sql = "SELECT s.skill_name
                  FROM user_skills us
                  JOIN skills s ON us.skill_id = s.skill_id
                 WHERE us.user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll() ?: [];
    }

    public function getLanguagesWithProf(int $userId): array
    {
        $sql = "SELECT l.language_name, ul.proficiency
                  FROM user_languages ul
                  JOIN languages l ON ul.language_id = l.language_id
                 WHERE ul.user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll() ?: [];
    }

    public function getJobCategories(int $userId): array
    {
        $sql = "SELECT c.category_name
                  FROM user_job_categories uj
                  JOIN job_categories c ON uj.category_id = c.category_id
                 WHERE uj.user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll() ?: [];
    }

    public function getRecentActivities(int $userId, int $limit = 3): array
    {
        $sql = "SELECT job_title, company_name, end_date
                  FROM user_experience
                 WHERE user_id = ?
              ORDER BY COALESCE(end_date, start_date) DESC
                 LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll() ?: [];
    }

    public function getReviews(int $userId, int $limit = 3): array
    {
        $sql = "SELECT reviewer_id, rating, review_text, created_at
                  FROM user_reviews
                 WHERE reviewed_user_id = ?
              ORDER BY created_at DESC
                 LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll() ?: [];
    }

    public function getPersonalInfo(int $userId): array
    {
        return $this->fetchOne("SELECT * FROM user_personal_info WHERE user_id = ? LIMIT 1", [$userId]);
    }

    public function savePersonalInfo(int $userId, array $data): bool
    {
        $existing = $this->getPersonalInfo($userId);
        if ($existing) {
            $sql = "UPDATE user_personal_info
                       SET birthdate = ?, gender = ?, nationality = ?, country = ?, city = ?, area = ?, about_me = ?, profile_picture = ?
                     WHERE user_id = ?";
            return $this->execute($sql, [
                $data['birthdate'],
                $data['gender'],
                $data['nationality'],
                $data['country'],
                $data['city'],
                $data['area'],
                $data['about_me'],
                $data['profile_picture'] ?? $existing['profile_picture'], // Keep existing if null
                $userId
            ]);
        }

        $sql = "INSERT INTO user_personal_info (user_id, birthdate, gender, nationality, country, city, area, about_me, profile_picture)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        return $this->execute($sql, [
            $userId,
            $data['birthdate'],
            $data['gender'],
            $data['nationality'],
            $data['country'],
            $data['city'],
            $data['area'],
            $data['about_me'],
            $data['profile_picture'] ?? null
        ]);
    }

    /* ------------ Education ------------ */
    public function getEducation(int $userId): array
    {
        return $this->fetchOne("SELECT * FROM user_education WHERE user_id = ? LIMIT 1", [$userId]);
    }

    public function saveEducation(int $userId, array $data): bool
    {
        $existing = $this->getEducation($userId);
        if ($existing) {
            $sql = "UPDATE user_education
                       SET education_level = ?, school_name = ?, certificate_name = ?, language_of_study = ?, graduation_year = ?, grade = ?
                     WHERE user_id = ?";
            return $this->execute($sql, [
                $data['education_level'],
                $data['school_name'],
                $data['certificate_name'] ?? '', // Added
                $data['language_of_study'],
                $data['graduation_year'],
                $data['grade'],
                $userId
            ]);
        }

        $sql = "INSERT INTO user_education (user_id, education_level, school_name, certificate_name, language_of_study, graduation_year, grade)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        return $this->execute($sql, [
            $userId,
            $data['education_level'],
            $data['school_name'],
            $data['certificate_name'] ?? '', // Added
            $data['language_of_study'],
            $data['graduation_year'],
            $data['grade']
        ]);
    }

    /* ------------ Experience ------------ */
    public function getExperiences(int $userId): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM user_experience WHERE user_id = ? ORDER BY exp_id ASC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll() ?: [];
    }

    public function replaceExperiences(int $userId, array $experiences): bool
    {
        $this->execute("DELETE FROM user_experience WHERE user_id = ?", [$userId]);
        if (!$experiences) {
            return true;
        }
        $sql = "INSERT INTO user_experience (user_id, years_of_experience, job_title, company_name, job_category, experience_type, start_date, end_date, working)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        foreach ($experiences as $exp) {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $userId,
                $exp['years_experience'],
                $exp['job_title'],
                $exp['company'],
                $exp['job_category'],
                $exp['experience_type'],
                $exp['start_date'],
                $exp['end_date'],
                $exp['working']
            ]);
        }
        return true;
    }

    /* ------------ Languages & Skills ------------ */
    private function getOrCreateLanguageId(string $languageName): ?int
    {
        if ($languageName === '') {
            return null;
        }
        $existing = $this->fetchOne("SELECT language_id FROM languages WHERE language_name = ? LIMIT 1", [$languageName]);
        if ($existing) {
            return (int)$existing['language_id'];
        }
        $this->execute("INSERT INTO languages (language_name) VALUES (?)", [$languageName]);
        return (int)$this->conn->lastInsertId();
    }

    private function getOrCreateSkillId(string $skillName): ?int
    {
        if ($skillName === '') {
            return null;
        }
        $existing = $this->fetchOne("SELECT skill_id FROM skills WHERE skill_name = ? LIMIT 1", [$skillName]);
        if ($existing) {
            return (int)$existing['skill_id'];
        }
        $this->execute("INSERT INTO skills (skill_name) VALUES (?)", [$skillName]);
        return (int)$this->conn->lastInsertId();
    }

    public function replaceLanguages(int $userId, array $languages, array $proficiencies): bool
    {
        $this->execute("DELETE FROM user_languages WHERE user_id = ?", [$userId]);
        $sql = "INSERT INTO user_languages (user_id, language_id, proficiency) VALUES (?, ?, ?)";
        foreach ($languages as $idx => $langName) {
            $langId = $this->getOrCreateLanguageId($langName);
            if (!$langId) {
                continue;
            }
            $prof = $proficiencies[$idx] ?? '';
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$userId, $langId, $prof]);
        }
        return true;
    }

    public function replaceSkills(int $userId, array $skills): bool
    {
        $this->execute("DELETE FROM user_skills WHERE user_id = ?", [$userId]);
        $sql = "INSERT INTO user_skills (user_id, skill_id) VALUES (?, ?)";
        foreach ($skills as $skillName) {
            $skillId = $this->getOrCreateSkillId($skillName);
            if (!$skillId) {
                continue;
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$userId, $skillId]);
        }
        return true;
    }

    /* ------------ CV ------------ */
    public function saveCv(int $userId, string $filename): bool
    {
        $existing = $this->fetchOne("SELECT id FROM user_cv WHERE user_id = ? LIMIT 1", [$userId]);
        if ($existing) {
            $sql = "UPDATE user_cv SET cv_filename = ?, uploaded_at = NOW() WHERE user_id = ?";
            return $this->execute($sql, [$filename, $userId]);
        }
        $sql = "INSERT INTO user_cv (user_id, cv_filename, uploaded_at) VALUES (?, ?, NOW())";
        return $this->execute($sql, [$userId, $filename]);
    }

    /* ------------ Career Interest ------------ */
    public function getCareerInterest(int $userId): array
    {
        return $this->fetchOne("SELECT * FROM user_career_interest WHERE user_id = ? LIMIT 1", [$userId]);
    }

    public function saveCareerInterest(int $userId, array $data): bool
    {
        // Try extended columns first (workplace/hide/public/push). If table lacks them, fall back.
        $baseParams = [
            $data['career_level'] ?? '',
            $data['job_type'] ?? '',
            $data['expected_salary'] ?? '',
            $data['workplace'] ?? null,
            $data['hide_salary'] ?? 0,
            $data['public_profile'] ?? 0,
            $data['push_notifications'] ?? 0,
        ];

        $existing = $this->getCareerInterest($userId);
        if ($existing) {
            // attempt extended update
            try {
                $sql = "UPDATE user_career_interest
                           SET career_level = ?, job_type = ?, expected_salary = ?, workplace = ?, hide_salary = ?, public_profile = ?, push_notifications = ?
                         WHERE user_id = ?";
                return $this->execute($sql, [...$baseParams, $userId]);
            } catch (PDOException $e) {
                // fallback to minimal columns
                $sql = "UPDATE user_career_interest
                           SET career_level = ?, job_type = ?, expected_salary = ?
                         WHERE user_id = ?";
                return $this->execute($sql, [
                    $data['career_level'] ?? '',
                    $data['job_type'] ?? '',
                    $data['expected_salary'] ?? '',
                    $userId
                ]);
            }
        }

        // insert path
        try {
            $sql = "INSERT INTO user_career_interest (user_id, career_level, job_type, expected_salary, workplace, hide_salary, public_profile, push_notifications)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            return $this->execute($sql, [
                $userId,
                ...$baseParams
            ]);
        } catch (PDOException $e) {
            $sql = "INSERT INTO user_career_interest (user_id, career_level, job_type, expected_salary)
                    VALUES (?, ?, ?, ?)";
            return $this->execute($sql, [
                $userId,
                $data['career_level'] ?? '',
                $data['job_type'] ?? '',
                $data['expected_salary'] ?? ''
            ]);
        }
    }

    /* ------------ Job Categories ------------ */
    private function getOrCreateCategoryId(string $name): ?int
    {
        if ($name === '') {
            return null;
        }
        $existing = $this->fetchOne("SELECT category_id FROM job_categories WHERE category_name = ? LIMIT 1", [$name]);
        if ($existing) {
            return (int)$existing['category_id'];
        }
        $this->execute("INSERT INTO job_categories (category_name) VALUES (?)", [$name]);
        return $this->conn->lastInsertId();
    }

    public function replaceJobCategories(int $userId, array $categories): bool
    {
        $this->execute("DELETE FROM user_job_categories WHERE user_id = ?", [$userId]);
        $sql = "INSERT INTO user_job_categories (user_id, category_id) VALUES (?, ?)";
        foreach ($categories as $catName) {
            $catId = $this->getOrCreateCategoryId($catName);
            if (!$catId) {
                continue;
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$userId, $catId]);
        }
        return true;
    }
}

