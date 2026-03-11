<?php

namespace App\Livewire;

use App\Models\Page;
use Illuminate\View\View;
use Livewire\Component;

class ContactPage extends Component
{
    public ?Page $page = null;
    public ?Page $aboutPage = null;

    public string $name = '';
    public string $email = '';
    public string $subject = '';
    public string $message = '';
    public bool $submitted = false;

    public function mount(): void
    {
        $this->page = Page::where('slug', 'contact-us')
            ->where('status', 'published')
            ->first();

        $this->aboutPage = Page::where('slug', 'about')
            ->where('status', 'published')
            ->first();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ];
    }

    public function submit(): void
    {
        $this->validate();

        // Here you would typically send an email or store the contact message
        // For now, we'll just mark it as submitted
        $this->submitted = true;

        $this->reset(['name', 'email', 'subject', 'message']);

        session()->flash('success', 'Thank you for your message. We will get back to you soon.');
    }

    public function render(): View
    {
        return view('livewire.contact-page')
            ->title(($this->page?->meta_title ?: $this->page?->title ?: 'Contact Us') . ' - Apricot Power')
            ->layoutData([
                'metaDescription' => $this->page?->meta_description ?: '',
            ])
            ->layout('layouts.storefront');
    }
}
