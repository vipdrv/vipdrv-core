import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { routing } from './pages.routing';
import { NgaModule } from '../theme/nga.module';
import { AppTranslationModule } from '../app.translation.module';

import { Pages } from './pages.component';
import { TextMaskModule } from 'angular2-text-mask';

@NgModule({
  imports: [CommonModule, AppTranslationModule, NgaModule, TextMaskModule, routing],
  declarations: [Pages]
})
export class PagesModule { }


