export type GeneralSettings = {
    site_name: string;
    site_slogan: string | null;
    email: string;
    sender_name: string;
    logo_light: string; // Helper'dan gelen Asset URL
    logo_dark: string;  // Asset URL
    favicon: string;    // Asset URL
};

export type GeneralSettingsForm = {
    site_name: string;
    site_slogan: string;
    email: string;
    sender_name: string;
    logo_light: File | null;
    logo_dark: File | null;
    favicon: File | null;
};
