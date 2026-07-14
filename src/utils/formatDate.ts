/**
 * Format tanggal ISO 8601 ke bahasa Indonesia
 * Contoh input: "2026-07-05T10:00:00+07:00"
 * Contoh output: "5 Juli 2026, 10:00 WIB"
 */
export function formatDate(dateString: string | null | undefined): string {
  if (!dateString) return 'Belum dijadwalkan';

  try {
    const date = new Date(dateString);
    if (isNaN(date.getTime())) {
      return 'Waktu tidak valid';
    }

    const day = date.getDate();
    const months = [
      'Januari',
      'Februari',
      'Maret',
      'April',
      'Mei',
      'Juni',
      'Juli',
      'Agustus',
      'September',
      'Oktober',
      'November',
      'Desember'
    ];
    const month = months[date.getMonth()];
    const year = date.getFullYear();

    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');

    return `${day} ${month} ${year}, ${hours}:${minutes} WIB`;
  } catch (error) {
    console.error('Error formatting date:', error);
    return 'Gagal memformat tanggal';
  }
}

export default formatDate;
