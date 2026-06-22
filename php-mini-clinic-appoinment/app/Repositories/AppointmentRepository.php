<?php

class AppointmentRepository
{
    public function __construct(private PDO $db) {}

    public function countAll(string $keyword = ''): int
    {
        $sql = "SELECT COUNT(*) AS total FROM appointments";

        if ($keyword !== '') {
            $sql .= " WHERE appointment_code LIKE :kw1
                      OR patient_name LIKE :kw2
                      OR patient_email LIKE :kw3";
            $kw = '%' . $keyword . '%';
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':kw1', $kw, PDO::PARAM_STR);
            $stmt->bindValue(':kw2', $kw, PDO::PARAM_STR);
            $stmt->bindValue(':kw3', $kw, PDO::PARAM_STR);
        } else {
            $stmt = $this->db->prepare($sql);
        }
        $stmt->execute();
        return (int) ($stmt->fetch()['total'] ?? 0);
    }

    public function getPaginated(string $keyword, int $limit, int $offset, string $sort, string $direction): array
    {
        $allowedSorts = ['id', 'appointment_code', 'patient_name', 'patient_email', 'appointment_date', 'status', 'created_at'];
        $allowedDirections = ['asc', 'desc'];

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'created_at';
        }
        if (!in_array(strtolower($direction), $allowedDirections, true)) {
            $direction = 'desc';
        }

        $sql = "SELECT id, appointment_code, patient_name, patient_email, appointment_date, status, created_at
                FROM appointments";

        if ($keyword !== '') {
            $sql .= " WHERE appointment_code LIKE :kw1
                      OR patient_name LIKE :kw2
                      OR patient_email LIKE :kw3";
        }

        $sql .= " ORDER BY {$sort} {$direction} LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        if ($keyword !== '') {
            $kw = '%' . $keyword . '%';
            $stmt->bindValue(':kw1', $kw, PDO::PARAM_STR);
            $stmt->bindValue(':kw2', $kw, PDO::PARAM_STR);
            $stmt->bindValue(':kw3', $kw, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM appointments WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): bool
    {
        $sql = "INSERT INTO appointments (appointment_code, patient_name, patient_email, appointment_date, status, note)
                VALUES (:appointment_code, :patient_name, :patient_email, :appointment_date, :status, :note)";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'appointment_code' => $data['appointment_code'],
                'patient_name' => $data['patient_name'],
                'patient_email' => $data['patient_email'] ?: null,
                'appointment_date' => $data['appointment_date'],
                'status' => $data['status'],
                'note' => $data['note'] ?: null,
            ]);
        } catch (PDOException $e) {
            if (($e->errorInfo[1] ?? null) === 1062) {
                throw new DuplicateRecordException('Appointment code already exists.');
            }
            throw $e;
        }
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE appointments
                SET appointment_code = :appointment_code, patient_name = :patient_name,
                    patient_email = :patient_email, appointment_date = :appointment_date,
                    status = :status, note = :note, updated_at = NOW()
                WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'id' => $id,
                'appointment_code' => $data['appointment_code'],
                'patient_name' => $data['patient_name'],
                'patient_email' => $data['patient_email'] ?: null,
                'appointment_date' => $data['appointment_date'],
                'status' => $data['status'],
                'note' => $data['note'] ?: null,
            ]);
        } catch (PDOException $e) {
            if (($e->errorInfo[1] ?? null) === 1062) {
                throw new DuplicateRecordException('Appointment code already exists.');
            }
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM appointments WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
