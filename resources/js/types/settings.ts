export type GeneralSettings = {
    site_name: string;
    site_slogan: string | null;
    email: string;
    sender_name: string;
    logo_light: string;
    logo_dark: string;
    favicon: string;
    default_language: string | null;
    default_currency: string | null;
    default_country: string | null;
    default_tax: string | null;
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
