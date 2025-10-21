import { Component, Input } from '@angular/core';
import { NgIf, NgFor } from '@angular/common';

export interface InfoBubbleItem {
  icon?: string;   
  title: string;
  text: string;
}

@Component({
  selector: 'app-info-bubble',
  standalone: true,
  imports: [NgIf, NgFor],
  templateUrl: './info-bubble.component.html',
  styleUrl: './info-bubble.component.scss'
})
export class InfoBubbleComponent {

  @Input() items: InfoBubbleItem[] = [];
  @Input() maxWidth = 1200;  
  @Input() gap = 18; 

}
